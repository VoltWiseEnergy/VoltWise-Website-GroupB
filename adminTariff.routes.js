const express = require('express');
const router = express.Router();
const controller = require('./adminTariff.controller');

// PBI-21
router.post('/set', controller.setTariff);

// PBI-22
router.put('/update/:id', controller.updateTariff);

// View tariff
router.get('/', controller.viewTariff);

// PBI-23
router.get('/history', controller.viewHistory);

module.exports = router;