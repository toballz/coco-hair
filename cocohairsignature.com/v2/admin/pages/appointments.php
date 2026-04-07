<?php
include("../config.php");

function adminEsc($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, "UTF-8");
}

function adminFormatDateKey(string $ymd): string
{
    if (!preg_match("/^\d{8}$/", $ymd)) {
        return $ymd !== "" ? $ymd : "N/A";
    }
    $d = DateTime::createFromFormat("Ymd", $ymd);
    return ($d instanceof DateTime) ? $d->format("D, M j, Y") : $ymd;
}

function adminFormatTimeLabel($raw): string
{
    $raw = trim((string) $raw);
    if ($raw === "") {
        return "N/A";
    }
    if (preg_match("/^\d{3,4}$/", $raw)) {
        $raw = str_pad($raw, 4, "0", STR_PAD_LEFT);
        $d = DateTime::createFromFormat("Hi", $raw);
        if ($d instanceof DateTime) {
            return $d->format("g:i A");
        }
    }
    return $raw;
}

$appointmentsByDate = [];
$sql = "SELECT
    pp.id_gen,
    pp.date_scheduled,
    pp.time_scheduled,
    pc.category_name,
    pl.id_ai AS product_id,
    pl.hair_name,
    pl.hair_images,
    pv.name AS variant_name
FROM product_purchased pp
LEFT JOIN product_variant pv ON pv.id_ai = pp.product_variant_id_ref
LEFT JOIN product_lists pl ON pl.id_ai = pv.product_list_id_ref
LEFT JOIN product_category pc ON pc.id_ai = pl.category
WHERE pp.haspaid = 1
ORDER BY pp.date_scheduled ASC, pp.time_scheduled ASC";

$result = $db->query($sql);
if ($result instanceof mysqli_result) {
    while ($row = $result->fetch_assoc()) {
        $dateKey = trim((string) ($row["date_scheduled"] ?? ""));
        if (!preg_match("/^\d{8}$/", $dateKey)) {
            continue;
        }

        $imageRef = preg_replace("/[^0-9a-zA-Z_-]/", "", (string) ($row["product_id"] ?? ""));
        $imageSet = json_decode((string) ($row["hair_images"] ?? ""), true);
        if (is_array($imageSet)) {
            foreach ($imageSet as $candidate) {
                $candidate = preg_replace("/[^0-9a-zA-Z_-]/", "", trim((string) $candidate));
                if ($candidate !== "") {
                    $imageRef = $candidate;
                    break;
                }
            }
        }
 
        $imageUrl = site::url_s3Host() . "/img/" . $imageRef . ".jpg?" . $recache;


        $serviceName = trim((string) ($row["hair_name"] ?? ""));
        if (trim((string) ($row["variant_name"] ?? "")) !== "") {
            $serviceName .= ($serviceName !== "" ? " - " : "") . trim((string) $row["variant_name"]);
        }
        if ($serviceName === "") {
            $serviceName = "Unknown service";
        }

        $appointmentsByDate[$dateKey][] = [
            "orderId" => (string) ($row["id_gen"] ?? ""),
            "imageUrl" => $imageUrl,
            "category" => trim((string) ($row["category_name"] ?? "Uncategorized")),
            "serviceName" => $serviceName,
            "dateLabel" => adminFormatDateKey($dateKey),
            "timeLabel" => adminFormatTimeLabel($row["time_scheduled"] ?? ""),
            "receiptUrl" => site::url_hostdir() . "/pages/receipt.php?orderId=" . urlencode((string) ($row["id_gen"] ?? "")),
        ];
    }
}

ksort($appointmentsByDate);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Appointments Calendar | Admin</title>
    <?php include("../template/_head.php"); ?>
    <style>
        .ap-calendar-shell {
            background: #fff;
            border: 1px solid var(--admin-border);
            border-radius: 14px;
            padding: 14px;
        }

        .ap-month-controls {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
            margin-bottom: 12px;
        }

        .ap-month-label {
            font-weight: 700;
            font-size: 1.1rem;
            margin: 0;
        }

        .ap-weekdays,
        .ap-calendar-grid {
            display: grid;
            grid-template-columns: repeat(7, minmax(0, 1fr));
            gap: 6px;
        }

        .ap-weekday {
            text-align: center;
            font-size: .78rem;
            color: var(--admin-muted);
            text-transform: uppercase;
            letter-spacing: .06em;
            font-weight: 700;
            padding: 6px 0;
        }

        .ap-day {
            border: 1px solid #e8ebf2;
            border-radius: 10px;
            min-height: 58px;
            background: #fff;
            color: #1e2533;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            align-items: flex-start;
            padding: 8px;
            transition: .2s ease;
        }

        .ap-day-num {
            font-weight: 700;
            line-height: 1;
        }

        .ap-day.disabled {
            opacity: .35;
            cursor: not-allowed;
            background: #f6f8fc;
        }

        .ap-day.booked {
            border-color: #f3c54c;
            background: #fffbef;
        }

        .ap-day.booked:hover {
            border-color: #e4ab16;
            transform: translateY(-1px);
        }

        .ap-day.selected {
            border-color: #111827;
            box-shadow: 0 0 0 2px rgba(17, 24, 39, .09);
        }

        .ap-count {
            font-size: .72rem;
            font-weight: 700;
            color: #8a5a0e;
            background: #f7d778;
            border-radius: 999px;
            padding: 2px 7px;
        }

        .ap-empty {
            border: 0;
            background: transparent;
            min-height: 58px;
        }

        .mini-receipt-card {
            border: 1px solid var(--admin-border);
            border-radius: 14px;
            overflow: hidden;
            height: 100%;
            background: #fff;
        }

        .mini-receipt-img {
            width: 100%;
            height: 180px;
            object-fit: cover;
            background: #f2f2f2;
        }

        .mini-receipt-meta {
            color: var(--admin-muted);
            font-size: .82rem;
            text-transform: uppercase;
            letter-spacing: .06em;
            font-weight: 700;
        }
    </style>
</head>

<body>
    <?php include("../template/_header.php"); ?>

    <section class="admin-section mb-3">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2">
            <h2 class="admin-section-title mb-0">Appointments Calendar</h2>
            <span class="text-muted-soft small">Only booked days are enabled</span>
        </div>

        <div class="ap-calendar-shell">
            <div class="ap-month-controls">
                <button class="btn btn-sm btn-outline-secondary" id="apPrevMonth" type="button">
                    <i class="bi bi-chevron-left"></i> Prev
                </button>
                <p class="ap-month-label" id="apMonthLabel">--</p>
                <button class="btn btn-sm btn-outline-secondary" id="apNextMonth" type="button">
                    Next <i class="bi bi-chevron-right"></i>
                </button>
            </div>

            <div class="ap-weekdays mb-1">
                <div class="ap-weekday">Sun</div>
                <div class="ap-weekday">Mon</div>
                <div class="ap-weekday">Tue</div>
                <div class="ap-weekday">Wed</div>
                <div class="ap-weekday">Thu</div>
                <div class="ap-weekday">Fri</div>
                <div class="ap-weekday">Sat</div>
            </div>

            <div class="ap-calendar-grid" id="apCalendarGrid"></div>
        </div>
    </section>

    <section class="admin-section">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2">
            <h2 class="admin-section-title mb-0">Mini Receipts</h2>
            <span class="text-muted-soft small" id="apSelectedDateLabel">Select a booked day</span>
        </div>
        <div class="row g-3" id="apMiniReceipts"></div>
    </section>

    <script>
        (function () {
            const appointmentMap = <?php echo json_encode($appointmentsByDate, JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
            const bookedKeys = Object.keys(appointmentMap).sort();

            const gridNode = document.getElementById("apCalendarGrid");
            const monthLabelNode = document.getElementById("apMonthLabel");
            const selectedDateLabelNode = document.getElementById("apSelectedDateLabel");
            const receiptsNode = document.getElementById("apMiniReceipts");
            const prevBtn = document.getElementById("apPrevMonth");
            const nextBtn = document.getElementById("apNextMonth");

            function escapeHtml(str) {
                return String(str)
                    .replaceAll("&", "&amp;")
                    .replaceAll("<", "&lt;")
                    .replaceAll(">", "&gt;")
                    .replaceAll("\"", "&quot;")
                    .replaceAll("'", "&#39;");
            }

            function keyToMonthIndex(key) {
                const y = Number(key.slice(0, 4));
                const m = Number(key.slice(4, 6)) - 1;
                return y * 12 + m;
            }

            function keyToDate(key) {
                return new Date(Number(key.slice(0, 4)), Number(key.slice(4, 6)) - 1, Number(key.slice(6, 8)));
            }

            function dateToKey(dateObj, day) {
                const y = dateObj.getFullYear();
                const m = String(dateObj.getMonth() + 1).padStart(2, "0");
                const d = String(day).padStart(2, "0");
                return `${y}${m}${d}`;
            }

            const now = new Date();
            const initialDate = bookedKeys.length > 0 ? keyToDate(bookedKeys[0]) : new Date(now.getFullYear(), now.getMonth(), 1);
            const state = {
                cursor: new Date(initialDate.getFullYear(), initialDate.getMonth(), 1),
                selectedKey: bookedKeys.length > 0 ? bookedKeys[0] : "",
                minMonth: bookedKeys.length > 0 ? keyToMonthIndex(bookedKeys[0]) : now.getFullYear() * 12 + now.getMonth(),
                maxMonth: bookedKeys.length > 0 ? keyToMonthIndex(bookedKeys[bookedKeys.length - 1]) : now.getFullYear() * 12 + now.getMonth(),
            };

            function getFirstBookedKeyInVisibleMonth() {
                const year = state.cursor.getFullYear();
                const month = String(state.cursor.getMonth() + 1).padStart(2, "0");
                const prefix = `${year}${month}`;
                return bookedKeys.find((k) => k.startsWith(prefix)) || "";
            }

            function renderMiniReceipts() {
                const selectedKey = state.selectedKey;
                const items = appointmentMap[selectedKey] || [];

                if (!selectedKey || items.length === 0) {
                    selectedDateLabelNode.textContent = "Select a booked day";
                    receiptsNode.innerHTML = '<div class="col-12"><div class="text-muted-soft py-2">No bookings for this day.</div></div>';
                    return;
                }

                selectedDateLabelNode.textContent = `${items.length} booking(s) on ${items[0].dateLabel}`;
                receiptsNode.innerHTML = items.map((item) => {
                    return `
                        <div class="col-12 col-md-6 col-xl-4">
                            <article class="mini-receipt-card">
                                <img class="mini-receipt-img" src="${escapeHtml(item.imageUrl)}" alt="${escapeHtml(item.serviceName)}">
                                <div class="p-3">
                                    <div class="mini-receipt-meta">${escapeHtml(item.category)}</div>
                                    <h3 class="h6 mt-1 mb-2">${escapeHtml(item.serviceName)}</h3>
                                    <div class="small text-muted-soft mb-2">
                                        <i class="bi bi-calendar-event me-1"></i>${escapeHtml(item.dateLabel)}
                                        <span class="mx-1">|</span>
                                        <i class="bi bi-clock me-1"></i>${escapeHtml(item.timeLabel)}
                                    </div>
                                    <a class="btn btn-sm btn-outline-secondary" target="_blank" rel="noopener noreferrer"
                                        href="${escapeHtml(item.receiptUrl)}">Receipt</a>
                                </div>
                            </article>
                        </div>
                    `;
                }).join("");
            }

            function renderCalendar() {
                const year = state.cursor.getFullYear();
                const month = state.cursor.getMonth();

                monthLabelNode.textContent = state.cursor.toLocaleString([], { month: "long", year: "numeric" });
                gridNode.innerHTML = "";

                const firstWeekday = new Date(year, month, 1).getDay();
                const daysInMonth = new Date(year, month + 1, 0).getDate();

                for (let i = 0; i < firstWeekday; i += 1) {
                    const empty = document.createElement("div");
                    empty.className = "ap-empty";
                    gridNode.appendChild(empty);
                }

                for (let day = 1; day <= daysInMonth; day += 1) {
                    const key = dateToKey(state.cursor, day);
                    const isBooked = Object.prototype.hasOwnProperty.call(appointmentMap, key);

                    const btn = document.createElement("button");
                    btn.type = "button";
                    btn.className = "ap-day";
                    btn.innerHTML = `<span class="ap-day-num">${day}</span>`;

                    if (isBooked) {
                        btn.classList.add("booked");
                        const count = appointmentMap[key].length;
                        btn.insertAdjacentHTML("beforeend", `<span class="ap-count">${count}</span>`);
                        btn.addEventListener("click", function () {
                            state.selectedKey = key;
                            renderCalendar();
                            renderMiniReceipts();
                        });
                    } else {
                        btn.classList.add("disabled");
                        btn.disabled = true;
                    }

                    if (key === state.selectedKey) {
                        btn.classList.add("selected");
                    }

                    gridNode.appendChild(btn);
                }

                const monthIndex = year * 12 + month;
                prevBtn.disabled = monthIndex <= state.minMonth;
                nextBtn.disabled = monthIndex >= state.maxMonth;
            }

            prevBtn.addEventListener("click", function () {
                state.cursor.setMonth(state.cursor.getMonth() - 1);
                state.selectedKey = getFirstBookedKeyInVisibleMonth();
                renderCalendar();
                renderMiniReceipts();
            });

            nextBtn.addEventListener("click", function () {
                state.cursor.setMonth(state.cursor.getMonth() + 1);
                state.selectedKey = getFirstBookedKeyInVisibleMonth();
                renderCalendar();
                renderMiniReceipts();
            });

            renderCalendar();
            renderMiniReceipts();
        })();
    </script>

    <?php include("../template/_footer.php"); ?>
</body>

</html>