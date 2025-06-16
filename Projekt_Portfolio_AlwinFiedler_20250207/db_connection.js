'use strict';

import nano from 'nano';
import db_credentials from './db_credentials.js';

const dbConnection = nano(`http://${db_credentials.username}:${db_credentials.password}@${db_credentials.url}`).db;
const db = dbConnection.use('portfolio');

export default db;