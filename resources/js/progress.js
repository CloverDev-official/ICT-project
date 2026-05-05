function updateProgress(type, current, total) {
    let percent = Math.floor((current / total) * 100);

    document.getElementById(`progressBar${type}`).style.width = percent + "%";
    document.getElementById(`progressText${type}`).innerText = `${current}/${total}`;
    document.getElementById(`progressPercent${type}`).innerText = percent + "%";
}

// contoh generate guru
function startGenerateGuru() {
    let total = 150;
    let current = 0;

    let interval = setInterval(() => {
        current++;

        updateProgress("Guru", current, total);

        if (current >= total) {
            clearInterval(interval);
        }
    }, 20);
}

// contoh generate murid
function startGenerateMurid() {
    let total = 1600;
    let current = 0;

    let interval = setInterval(() => {
        current++;

        updateProgress("Murid", current, total);

        if (current >= total) {
            clearInterval(interval);
        }
    }, 5);
}