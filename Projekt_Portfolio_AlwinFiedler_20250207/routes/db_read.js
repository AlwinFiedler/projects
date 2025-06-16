'use strict';

import express from 'express';
import db from '../db_connection.js';

const router = express.Router();

router.get('/', async (req, res) => {
    try {
      const documentId = '623094e64c79d9900bebd56e7e000d26';
      const document = await db.get(documentId);  

      res.render('portfolio', { document });
    } catch (error) {
      res.status(500).send(`Error fetching document: ${error.message}`);
    }
  });

  export default router;