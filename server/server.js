import { Api, TelegramClient } from "telegram";
import { StringSession } from "telegram/sessions/index.js";
import dotenv from 'dotenv';
// const input = require("input"); // npm i input
import input from 'input';

dotenv.config();

const apiId = parseInt(process.env.API_ID);
const apiHash = process.env.API_HASH;
const stringSession = new StringSession(process.env.STRING_SESSION);

const client = new TelegramClient(stringSession, apiId, apiHash);

(async () => {
    console.log("Loading interactive example...");
    const client = new TelegramClient(stringSession, apiId, apiHash, {
      connectionRetries: 5,
    });

    try {
      await client.connect();
      console.log("Connected successfully!");
      await client.sendMessage("me", { message: "Connected!" });
    } catch (error) {
      console.error("Failed to connect to Telegram:", error);
      process.exit(1);
    }
    
  })();