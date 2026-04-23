<?php
include("../config.php");

function adminEsc($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, "UTF-8");
}

function normalizeOverrideTimes(string $rawInput, array &$errors): string
{
    $rawInput = trim($rawInput);
    if ($rawInput === "") {
        return "";
    }

    $parts = array_map("trim", explode(",", $rawInput));
    $times = [];

    foreach ($parts as $part) {
        if ($part === "") {
            continue;
        }
        if (!preg_match("/^\d{3,4}$/", $part)) {
            $errors[] = "Invalid time '" . $part . "'. Use HHMM format.";
            continue;
        }

        $time = str_pad($part, 4, "0", STR_PAD_LEFT);
        $hour = (int) substr($time, 0, 2);
        $minute = (int) substr($time, 2, 2);
        if ($hour > 23 || $minute > 59) {
            $errors[] = "Out-of-range time '" . $part . "'.";
            continue;
        }

        $times[] = $time;
    }

    $times = array_values(array_unique($times));
    return implode(", ", $times);
}

function normalizeDateKey(string $rawDate): string
{
    $rawDate = preg_replace("/[^0-9]/", "", trim($rawDate));
    if (preg_match("/^\d{8}$/", $rawDate)) {
        return $rawDate;
    }
    return "";
}

function dateKeyToLabel(string $dateKey): string
{
    if (!preg_match("/^\d{8}$/", $dateKey)) {
        return $dateKey;
    }
    $d = DateTime::createFromFormat("Ymd", $dateKey);
    return ($d instanceof DateTime) ? $d->format("D, M j, Y") : $dateKey;
}

$flashSuccess = "";
$errors = [];

$overrideRowId = 0;
$overrideMap = []; // date(YYYYMMDD) => time CSV

$query = $db->query("SELECT id_ai, description FROM availability WHERE namer = 'override' LIMIT 1");
if ($query instanceof mysqli_result && $query->num_rows === 1) {
    $row = $query->fetch_assoc();
    $overrideRowId = (int) ($row["id_ai"] ?? 0);
    $decoded = json_decode((string) ($row["description"] ?? "[]"), true);

    if (is_array($decoded)) {
        foreach ($decoded as $item) {
            if (!is_array($item)) {
                continue;
            }
            $dateKey = normalizeDateKey((string) ($item["date"] ?? ""));
            if ($dateKey === "") {
                continue;
            }
            $timeCsv = trim((string) ($item["time"] ?? ""));
            $overrideMap[$dateKey] = $timeCsv;
        }
    }
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST["save_override"])) {
        $selectedDate = normalizeDateKey((string) ($_POST["selected_date"] ?? ""));
        $timeInput = (string) ($_POST["selected_times"] ?? "");

        if ($selectedDate === "") {
            $errors[] = "Please select a valid date from the calendar.";
        } else {
            $normalizedTimes = normalizeOverrideTimes($timeInput, $errors);
            if (count($errors) === 0) {
                if ($normalizedTimes === "") {
                    $overrideMap[$selectedDate] = "";
                    $flashSuccess = "Marked " . dateKeyToLabel($selectedDate) . " as unavailable.";
                } else {
                    $overrideMap[$selectedDate] = $normalizedTimes;
                    $flashSuccess = "Override saved for " . dateKeyToLabel($selectedDate) . ".";
                }
            }
        }
    }

    if (isset($_POST["delete_override"])) {
        $deleteDate = normalizeDateKey((string) ($_POST["delete_date"] ?? ""));
        if ($deleteDate === "") {
            $errors[] = "Delete failed: invalid date.";
        } elseif (!isset($overrideMap[$deleteDate])) {
            $errors[] = "Delete failed: date not found in overrides.";
        } else {
            unset($overrideMap[$deleteDate]);
            $flashSuccess = "Deleted override for " . dateKeyToLabel($deleteDate) . ".";
        }
    }

    if (count($errors) === 0 && (isset($_POST["save_override"]) || isset($_POST["delete_override"]))) {
        ksort($overrideMap);
        $payload = [];
        foreach ($overrideMap as $dateKey => $timeCsv) {
            $payload[] = [
                "date" => $dateKey,
                "time" => $timeCsv,
            ];
        }

        $json = json_encode($payload, JSON_UNESCAPED_SLASHES);
        if ($json === false) {
            $errors[] = "Failed to encode override JSON.";
        } else {
            $safeJson = mysqli_real_escape_string($db, $json);

            if ($overrideRowId > 0) {
                $sql = "UPDATE availability SET description = '$safeJson' WHERE id_ai = '$overrideRowId' AND namer = 'override' LIMIT 1";
                $ok = $db->query($sql);
            } else {
                $sql = "INSERT INTO availability (namer, description, extra1) VALUES ('override', '$safeJson', '')";
                $ok = $db->query($sql);
                if ($ok) {
                    $overrideRowId = (int) $db->insert_id;
                }
            }

            if (!$ok) {
                $errors[] = "Database update failed.";
                $flashSuccess = "";
            }
        }
    }
}

ksort($overrideMap);
$overrideItems = [];
foreach ($overrideMap as $dateKey => $timeCsv) {
    $overrideItems[] = [
        "date" => $dateKey,
        "dateLabel" => dateKeyToLabel($dateKey),
        "time" => $timeCsv,
        "timeLabel" => ($timeCsv === "" ? "Unavailable" : $timeCsv),
    ];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Override Availability | Admin</title>
    <?php include("../template/_head.php"); ?>
    <style>
        .ov-notes {
            border-left: 4px solid #f0b429;
            background: #fff8e6;
            border-radius: 10px;
            padding: 10px 12px;
        }

        .ov-cal-shell {
            background: #fff;
            border: 1px solid var(--admin-border);
            border-radius: 14px;
            padding: 14px;
        }

        .ov-month-controls {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
            margin-bottom: 10px;
        }

        .ov-month-label {
            margin: 0;
            font-weight: 700;
            font-size: 1.08rem;
        }

        .ov-weekdays,
        .ov-grid {
            display: grid;
            grid-template-columns: repeat(7, minmax(0, 1fr));
            gap: 6px;
        }

        .ov-weekday {
            text-align: center;
            font-size: .78rem;
            color: var(--admin-muted);
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .05em;
            padding: 4px 0;
        }

        .ov-day,
        .ov-empty {
            min-height: 56px;
            border-radius: 10px;
        }

        .ov-empty {
            border: 0;
            background: transparent;
        }

        .ov-day {
            border: 1px solid #e8ebf2;
            background: #fff;
            text-align: left;
            padding: 8px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            cursor: pointer;
            transition: .18s ease;
        }

        .ov-day:hover {
            transform: translateY(-1px);
            border-color: #cfd6e6;
        }

        .ov-day.has-override {
            border-color: #f0be44;
            background: #fff9ea;
        }

        .ov-day.selected {
            border-color: #111827;
            box-shadow: 0 0 0 2px rgba(17, 24, 39, .08);
        }

        .ov-day-num {
            font-weight: 700;
            line-height: 1;
        }

        .ov-badge {
            display: inline-block;
            border-radius: 999px;
            font-size: .7rem;
            padding: 2px 7px;
            background: #f9d77a;
            color: #784f00;
            font-weight: 700;
        }

        .ov-item {
            border: 1px solid var(--admin-border);
            border-radius: 12px;
            padding: 10px 12px;
            background: #fff;
        }
    </style>
</head>
<body>
    <?php include("../template/_header.php"); ?>

    <section class="admin-section mb-3">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2">
            <h2 class="admin-section-title mb-0">Override Specific Dates</h2>
            <span class="small text-muted-soft">Edits `availability.namer = override`</span>
        </div>
        <div class="ov-notes">
            <div class="fw-semibold">Leave empty for unavailability</div>
            <div class="small">(24hrs clock)</div>
            <div class="small">Click a date and enter only the time(s) you will be available for that date.</div>
        </div>
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

    <section class="row g-3">
        <div class="col-12 col-xl-7">
            <div class="admin-section">
                <h3 class="h6 text-uppercase fw-bold mb-2">Calendar</h3>
                <div class="ov-cal-shell">
                    <div class="ov-month-controls">
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="ovPrevMonth">
                            <i class="bi bi-chevron-left"></i> Prev
                        </button>
                        <p class="ov-month-label" id="ovMonthLabel">--</p>
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="ovNextMonth">
                            Next <i class="bi bi-chevron-right"></i>
                        </button>
                    </div>
                    <div class="ov-weekdays mb-1">
                        <div class="ov-weekday">Sun</div>
                        <div class="ov-weekday">Mon</div>
                        <div class="ov-weekday">Tue</div>
                        <div class="ov-weekday">Wed</div>
                        <div class="ov-weekday">Thu</div>
                        <div class="ov-weekday">Fri</div>
                        <div class="ov-weekday">Sat</div>
                    </div>
                    <div class="ov-grid" id="ovGrid"></div>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-5">
            <form method="post" class="admin-section">
                <h3 class="h6 text-uppercase fw-bold mb-2">Edit Selected Date</h3>
                <input type="hidden" name="save_override" value="1">
                <input type="hidden" name="selected_date" id="selectedDateInput" value="">

                <div class="mb-2">
                    <div class="small text-muted-soft">Selected Date</div>
                    <div class="fw-semibold" id="selectedDateLabel">None</div>
                </div>

                <label class="form-label" for="selectedTimesInput">Available Times</label>
                <input type="text" class="form-control" id="selectedTimesInput" name="selected_times"
                    placeholder="0830, 1130, 1630">
                <div class="form-help mt-1">Comma-separated HHMM times. Leave empty for unavailability.</div>

                <div class="d-flex flex-wrap gap-2 mt-3">
                    <button type="submit" class="btn btn-dark">
                        <i class="bi bi-save me-1"></i> Save Date Override
                    </button>
                </div>
            </form>
        </div>
    </section>

    <section class="admin-section mt-3">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2">
            <h3 class="h6 text-uppercase fw-bold mb-0">Current Override Entries</h3>
            <span class="small text-muted-soft"><?php echo count($overrideItems); ?> item(s)</span>
        </div>

        <?php if (count($overrideItems) === 0) { ?>
            <div class="text-muted-soft">No override dates set.</div>
        <?php } else { ?>
            <div class="row g-2">
                <?php foreach ($overrideItems as $item) { ?>
                    <div class="col-12 col-md-6 col-xl-4">
                        <div class="ov-item">
                            <div class="fw-semibold"><?php echo adminEsc($item["dateLabel"]); ?></div>
                            <div class="small text-muted-soft mb-2"><?php echo adminEsc($item["date"]); ?></div>
                            <div class="mb-2"><?php echo adminEsc($item["timeLabel"]); ?></div>
                            <form method="post" class="d-inline">
                                <input type="hidden" name="delete_override" value="1">
                                <input type="hidden" name="delete_date" value="<?php echo adminEsc($item["date"]); ?>">
                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete override">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                <?php } ?>
            </div>
        <?php } ?>
    </section>

    <script>
        (function () {
            const overrideMap = {};
            const overrideItems = <?php echo json_encode($overrideItems, JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
            overrideItems.forEach(function (item) {
                overrideMap[item.date] = item.time;
            });

            const gridNode = document.getElementById("ovGrid");
            const monthLabelNode = document.getElementById("ovMonthLabel");
            const selectedDateInput = document.getElementById("selectedDateInput");
            const selectedDateLabel = document.getElementById("selectedDateLabel");
            const selectedTimesInput = document.getElementById("selectedTimesInput");
            const prevBtn = document.getElementById("ovPrevMonth");
            const nextBtn = document.getElementById("ovNextMonth");

            function keyToDate(key) {
                return new Date(Number(key.slice(0, 4)), Number(key.slice(4, 6)) - 1, Number(key.slice(6, 8)));
            }

            function dateToKey(dateObj, day) {
                const y = dateObj.getFullYear();
                const m = String(dateObj.getMonth() + 1).padStart(2, "0");
                const d = String(day).padStart(2, "0");
                return `${y}${m}${d}`;
            }

            function keyToLabel(key) {
                if (!/^\d{8}$/.test(key)) {
                    return key;
                }
                const dateObj = keyToDate(key);
                return dateObj.toLocaleDateString([], { weekday: "short", month: "short", day: "numeric", year: "numeric" });
            }

            const overrideKeys = Object.keys(overrideMap).sort();
            const now = new Date();
            const initialDate = overrideKeys.length ? keyToDate(overrideKeys[0]) : now;
            const state = {
                cursor: new Date(initialDate.getFullYear(), initialDate.getMonth(), 1),
                selectedKey: overrideKeys.length ? overrideKeys[0] : dateToKey(now, now.getDate()),
            };

            function syncEditor() {
                selectedDateInput.value = state.selectedKey;
                selectedDateLabel.textContent = keyToLabel(state.selectedKey);
                selectedTimesInput.value = overrideMap[state.selectedKey] || "";
            }

            function renderCalendar() {
                const year = state.cursor.getFullYear();
                const month = state.cursor.getMonth();
                const firstWeekday = new Date(year, month, 1).getDay();
                const daysInMonth = new Date(year, month + 1, 0).getDate();

                monthLabelNode.textContent = state.cursor.toLocaleString([], { month: "long", year: "numeric" });
                gridNode.innerHTML = "";

                for (let i = 0; i < firstWeekday; i += 1) {
                    const empty = document.createElement("div");
                    empty.className = "ov-empty";
                    gridNode.appendChild(empty);
                }

                for (let day = 1; day <= daysInMonth; day += 1) {
                    const key = dateToKey(state.cursor, day);
                    const hasOverride = Object.prototype.hasOwnProperty.call(overrideMap, key);

                    const btn = document.createElement("button");
                    btn.type = "button";
                    btn.className = "ov-day" + (hasOverride ? " has-override" : "") + (state.selectedKey === key ? " selected" : "");
                    btn.innerHTML = `<span class="ov-day-num">${day}</span>${hasOverride ? '<span class="ov-badge">override</span>' : ""}`;
                    btn.addEventListener("click", function () {
                        state.selectedKey = key;
                        syncEditor();
                        renderCalendar();
                    });

                    gridNode.appendChild(btn);
                }
            }

            prevBtn.addEventListener("click", function () {
                state.cursor.setMonth(state.cursor.getMonth() - 1);
                renderCalendar();
            });

            nextBtn.addEventListener("click", function () {
                state.cursor.setMonth(state.cursor.getMonth() + 1);
                renderCalendar();
            });

            syncEditor();
            renderCalendar();
        })();
    </script>

    <?php include("../template/_footer.php"); ?>
</body>
</html>
