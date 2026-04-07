(function () {
    var nowNode = document.getElementById("adminNow");

    function updateNow() {
        if (!nowNode) {
            return;
        }
        var now = new Date();
        nowNode.textContent = now.toLocaleString([], {
            year: "numeric",
            month: "short",
            day: "2-digit",
            hour: "2-digit",
            minute: "2-digit",
        });
    }

    updateNow();
    setInterval(updateNow, 30000);

    var filterInputs = document.querySelectorAll("[data-table-filter]");
    filterInputs.forEach(function (input) {
        var selector = input.getAttribute("data-table-target");
        var table = selector ? document.querySelector(selector) : null;
        if (!table) {
            return;
        }
        var rows = table.querySelectorAll("tbody tr");
        input.addEventListener("input", function () {
            var q = input.value.trim().toLowerCase();
            rows.forEach(function (row) {
                var matches = row.textContent.toLowerCase().indexOf(q) !== -1;
                row.style.display = matches ? "" : "none";
            });
        });
    });
})();
