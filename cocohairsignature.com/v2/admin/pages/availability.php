<?php
include("../config.php");

function adminEsc($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, "UTF-8");
}

function normalizeTimesForDay(string $rawInput, string $dayLabel, array &$errors): string
{
    $rawInput = trim($rawInput);
    if ($rawInput === "") {
        return "";
    }

    $parts = array_map("trim", explode(",", $rawInput));
    $out = [];

    foreach ($parts as $part) {
        if ($part === "") {
            continue;
        }
        if (!preg_match("/^\d{3,4}$/", $part)) {
            $errors[] = $dayLabel . ": '" . $part . "' is invalid. Use HHMM format, e.g. 0830.";
            continue;
        }

        $time = str_pad($part, 4, "0", STR_PAD_LEFT);
        $hour = (int) substr($time, 0, 2);
        $minute = (int) substr($time, 2, 2);
        if ($hour > 23 || $minute > 59) {
            $errors[] = $dayLabel . ": '" . $part . "' is out of range.";
            continue;
        }

        $out[] = $time;
    }

    $out = array_values(array_unique($out));
    return implode(", ", $out);
}

$weekdays = [
    "monday" => "Monday",
    "tuesday" => "Tuesday",
    "wednesday" => "Wednesday",
    "thursday" => "Thursday",
    "friday" => "Friday",
    "saturday" => "Saturday",
    "sunday" => "Sunday",
];

$weeklyData = [];
foreach ($weekdays as $dayKey => $dayLabel) {
    $weeklyData[$dayKey] = "";
}

$weeklyRowId = 0;
$flashSuccess = "";
$errors = [];

$query = $db->query("SELECT id_ai, description FROM availability WHERE namer = 'weekly' LIMIT 1");
if ($query instanceof mysqli_result && $query->num_rows === 1) {
    $row = $query->fetch_assoc();
    $weeklyRowId = (int) ($row["id_ai"] ?? 0);
    $decoded = json_decode((string) ($row["description"] ?? "{}"), true);
    if (is_array($decoded)) {
        foreach ($weekdays as $dayKey => $dayLabel) {
            $weeklyData[$dayKey] = trim((string) ($decoded[$dayKey] ?? ""));
        }
    }
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["save_weekly"])) {
    $newWeeklyData = [];

    foreach ($weekdays as $dayKey => $dayLabel) {
        $rawValue = (string) ($_POST[$dayKey] ?? "");
        $newWeeklyData[$dayKey] = normalizeTimesForDay($rawValue, $dayLabel, $errors);
    }

    if (count($errors) === 0) {
        $json = json_encode($newWeeklyData, JSON_UNESCAPED_SLASHES);
        if ($json === false) {
            $errors[] = "Failed to encode weekly availability JSON.";
        } else {
            $safeJson = mysqli_real_escape_string($db, $json);

            if ($weeklyRowId > 0) {
                $sql = "UPDATE availability SET description = '$safeJson' WHERE id_ai = '$weeklyRowId' AND namer = 'weekly' LIMIT 1";
                $ok = $db->query($sql);
            } else {
                $sql = "INSERT INTO availability (namer, description, extra1) VALUES ('weekly', '$safeJson', '')";
                $ok = $db->query($sql);
                if ($ok) {
                    $weeklyRowId = (int) $db->insert_id;
                }
            }

            if ($ok) {
                $weeklyData = $newWeeklyData;
                $flashSuccess = "Weekly availability updated successfully.";
            } else {
                $errors[] = "Database update failed.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Availability | Admin</title>
    <?php include("../template/_head.php"); ?>
    <style>
        .day-card {
            border: 1px solid var(--admin-border);
            border-radius: 12px;
            padding: 12px;
            background: #fff;
            height: 100%;
        }

        .day-card .form-label {
            font-weight: 700;
            margin-bottom: 6px;
        }

        .time-preview-wrap {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            margin-top: 8px;
        }

        .time-preview {
            display: inline-block;
            background: #f3f4f6;
            border: 1px solid #e5e7eb;
            border-radius: 999px;
            font-size: .75rem;
            padding: 2px 8px;
            color: #374151;
        }
    </style>
</head>
<body>
    <?php include("../template/_header.php"); ?>

    <section class="admin-section mb-3">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2">
            <h2 class="admin-section-title mb-0">Weekly Availability</h2>
            <span class="small text-muted-soft">Edits `availability.namer = weekly`</span>
        </div>
        <p class="text-muted-soft mb-0">
            Enter comma-separated 24-hour times (`HHMM`) for each day. Example: <code>0830, 1130, 1630</code>.
            Leave a day blank to mark it unavailable.
        </p>
    </section>

    <?php if ($flashSuccess !== "") { ?>
        <div class="alert alert-success"><?php echo adminEsc($flashSuccess); ?></div>
    <?php } ?>

    <?php if (count($errors) > 0) { ?>
        <div class="alert alert-danger">
            <div class="fw-semibold mb-1">Please fix the following:</div>
            <ul class="mb-0">
                <?php foreach ($errors as $error) { ?>
                    <li><?php echo adminEsc($error); ?></li>
                <?php } ?>
            </ul>
        </div>
    <?php } ?>

    <form method="post" class="admin-section mb-3">
        <input type="hidden" name="save_weekly" value="1">

        <div class="row g-3">
            <?php foreach ($weekdays as $dayKey => $dayLabel) { ?>
                <?php $dayValue = (string) ($weeklyData[$dayKey] ?? ""); ?>
                <div class="col-12 col-md-6 col-xl-4">
                    <div class="day-card">
                        <label class="form-label" for="<?php echo adminEsc($dayKey); ?>">
                            <?php echo adminEsc($dayLabel); ?>
                        </label>
                        <input type="text" class="form-control" id="<?php echo adminEsc($dayKey); ?>"
                            name="<?php echo adminEsc($dayKey); ?>" value="<?php echo adminEsc($dayValue); ?>"
                            placeholder="0830, 1130, 1630">
                        <div class="form-help mt-1">24-hour format, comma-separated.</div>

                        <div class="time-preview-wrap">
                            <?php
                            $previewTimes = array_filter(array_map("trim", explode(",", $dayValue)));
                            if (count($previewTimes) === 0) {
                                echo '<span class="time-preview">No slots</span>';
                            } else {
                                foreach ($previewTimes as $slot) {
                                    echo '<span class="time-preview">' . adminEsc($slot) . "</span>";
                                }
                            }
                            ?>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>

        <div class="d-flex flex-wrap gap-2 mt-3">
            <button type="submit" class="btn btn-dark">
                <i class="bi bi-save me-1"></i> Save Weekly Availability
            </button>
            <a class="btn btn-outline-secondary" href="pages/appointments.php">View Appointments Calendar</a>
        </div>
    </form>

    <section class="admin-section">
        <h3 class="h6 text-uppercase fw-bold mb-2">Current JSON Preview</h3>
        <pre class="mb-0" style="max-height: 260px; overflow: auto;"><?php
            echo adminEsc(json_encode($weeklyData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
            ?></pre>
    </section>

    <?php include("../template/_footer.php"); ?>
</body>
</html>
