const express = require('express');
const app = express();

const adminTariffRoutes = require('./adminTariff.routes');

app.use(express.json());
app.use('/admin/tariff', adminTariffRoutes);

app.listen(3000, () => {
    console.log('Admin Tariff Management running on http://localhost:3000');
});