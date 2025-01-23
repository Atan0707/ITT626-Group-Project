import { Api, TelegramClient } from "telegram";
import { StringSession } from "telegram/sessions/index.js";
import dotenv from 'dotenv';
// const input = require("input"); // npm i input
import input from 'input';
import express from 'express';
import cors from 'cors';

dotenv.config();

const app = express();
app.use(cors());
app.use(express.json());

const apiId = parseInt(process.env.API_ID);
const apiHash = process.env.API_HASH;
const stringSession = new StringSession(process.env.STRING_SESSION);

const client = new TelegramClient(stringSession, apiId, apiHash, {
  connectionRetries: 5,
});

// Initialize Telegram client
(async () => {
  console.log("Loading interactive example...");
  
  try {
    await client.connect();
    console.log("Connected successfully!");
    // await client.sendMessage("me", { message: "Server started!" });
  } catch (error) {
    console.error("Failed to connect to Telegram:", error);
    process.exit(1);
  }
})();

// API endpoint to send messages after adding parcel to database
app.post('/receive-parcel', async (req, res) => {
  try {
    const { phoneNumber, message } = req.body;
    
    if (!phoneNumber || !message) {
      return res.status(400).json({ error: 'Phone number and message are required' });
    }

    // Validate phone number format
    if (!phoneNumber.startsWith('+60')) {
      return res.status(400).json({ error: 'Phone number must start with +60' });
    }

    // Send message to the user
    const result = await client.sendMessage(phoneNumber, { message });
    
    res.json({ success: true, messageId: result.id });
  } catch (error) {
    console.error('Error sending message:', error);
    res.status(500).json({ error: 'Failed to send message' });
  }
});

const PORT = process.env.PORT || 3000;
app.listen(PORT, () => {
  console.log(`Server running on port ${PORT}`);
});