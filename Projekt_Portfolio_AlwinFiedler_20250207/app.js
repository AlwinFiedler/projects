'use strict';

import express from 'express';
import bodyParser from 'body-parser';
import readRoutes from './routes/db_read.js';
import writeRoutes from './routes/db_write.js';

const app = express();
const port = 80;

app.use(bodyParser.urlencoded({ extended: true }));
app.use(bodyParser.json());
app.use(express.static('public'));
app.set('view engine', 'ejs');

app.use('/', readRoutes);
app.use('/', writeRoutes);

app.listen(port, () => {
    console.log(`Server is running on http://localhost:${port}`);
  });