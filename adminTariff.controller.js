const service = require('./adminTariff.service');

exports.setTariff = (req, res) => {
    const { rate_per_kwh } = req.body;
    res.json(service.setTariff(rate_per_kwh));
};

exports.updateTariff = (req, res) => {
    const { id } = req.params;
    const { rate_per_kwh } = req.body;

    const result = service.updateTariff(id, rate_per_kwh);
    if (!result) return res.status(404).json({ message: "Tariff not found" });

    res.json(result);
};

exports.viewTariff = (req, res) => {
    res.json(service.viewTariff());
};

exports.viewHistory = (req, res) => {
    res.json(service.viewHistory());
};