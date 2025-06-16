'use strict';

import express from 'express';
import nodemailer from 'nodemailer';
import db from '../db_connection.js';
import email_credentials from '../email.json' with {type: 'json'};

const router = express.Router();

/*router.post('/submit-form', async (req, res) => {
  console.log(req.body);
  const { username, phonenumber, emailaddress, textmessage } = req.body;

  if (!username || !emailaddress || !textmessage) {
    return res.status(400).send("These fields are required.");
  }

  try {  
    const existingDoc = await db.get("623094e64c79d9900bebd56e7e015c99");
  
    if (!Array.isArray(existingDoc.contactForm)) {
      existingDoc.contactForm = [];
    }
    
    existingDoc.contactForm.push({
      username,
      phonenumber,
      emailaddress,
      textmessage,
      submittedAt: new Date().toISOString(),
    });
    
    const response = await db.insert(existingDoc);
    console.log("Document updated:", response);

    res.status(200).send('Form submitted successfully!');
  } catch (error) {
    console.error("Error updating document:", error);
    res.status(500).send("An error occurred while submitting the form.");
  }
});*/

// Es folgt meine ursprüngliche router.post zur Formular-Übertragung mit Schreiben auf die Datenbank
// bei der zusätzlich auch noch eine E-Mail generiert werden sollte.
// Leider entsteht dabei folgender Fehler:
// Error occurred while sending email: connect ETIMEDOUT 108.177.15.109:465
// Zum Testen folgenden auskommentierten Abschnitt einkommentieren
// und obige router.post auskommentieren.

const transporter = nodemailer.createTransport({
  service: 'gmail',
  port: 587,
  secure: false,
  auth: {
    user: email_credentials.email,
    pass: email_credentials.password,
  },
});

router.post('/submit-form', async (req, res) => {
  try {
    console.log(req.body);
    const { username, phonenumber, emailaddress, textmessage } = req.body;

    if (!username || !emailaddress || !textmessage) {
      return res.status(400).send("These fields are required.");
    }

    const existingDoc = await db.get("623094e64c79d9900bebd56e7e015c99");

    if (!Array.isArray(existingDoc.contactForm)) {
      existingDoc.contactForm = [];
    }

    existingDoc.contactForm.push({
      username,
      phonenumber,
      emailaddress,
      textmessage,
      submittedAt: new Date().toISOString(),
    });

    const response = await db.insert(existingDoc);
    console.log("Document updated:", response);

    const mailOptions = {
      from: email_credentials.email,
      to: 'recipient-email@example.com',
      subject: 'New Form Submission',
      text: `
        You have received a new form submission:
        
        Name: ${username}
        Phone Number: ${phonenumber || 'Not provided'}
        Email: ${emailaddress}
        Message: ${textmessage}
        
        Submitted At: ${new Date().toLocaleString()}
      `,
    };

    transporter.sendMail(mailOptions, (error, info) => {
      if (error) {
        console.error('Error occurred while sending email:', error.message);
        return res.status(500).send('Error sending email: ' + error.message);
      }
      console.log('Email sent:', info.messageId);
      res.status(200).send('Form submitted and email sent successfully!');
    });
  } catch (error) {
    console.error("Error updating document:", error);
    res.status(500).send("An error occurred while submitting the form.");
  }
});

export default router;