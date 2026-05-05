let tariffs = [];
let histories = [];

function setTariff(rate_per_kwh) {
    const oldRate = tariffs.length ? tariffs[tariffs.length - 1].rate_per_kwh : null;

    const newTariff = {
        id: tariffs.length + 1,
        rate_per_kwh,
        created_at: new Date()
    };

    tariffs.push(newTariff);

    histories.push({
        old_rate: oldRate,
        new_rate: rate_per_kwh,
        changed_at: new Date()
    });

    return newTariff;
}

function updateTariff(id, rate_per_kwh) {
    const tariff = tariffs.find(t => t.id == id);
    if (!tariff) return null;

    histories.push({
        old_rate: tariff.rate_per_kwh,
        new_rate: rate_per_kwh,
        changed_at: new Date()
    });

    tariff.rate_per_kwh = rate_per_kwh;
    return tariff;
}

function viewTariff() {
    return tariffs;
}

function viewHistory() {
    return histories;
}

module.exports = {
    setTariff,
    updateTariff,
    viewTariff,
    viewHistory
};