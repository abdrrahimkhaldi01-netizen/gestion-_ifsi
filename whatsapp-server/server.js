const { default: makeWASocket, useMultiFileAuthState } = require('@whiskeysockets/baileys');
const express = require('express');
const qrcode = require('qrcode');
const cors = require('cors');
const fs = require('fs');

const app = express();
app.use(express.json());
app.use(cors());

let sock = null;
let isReady = false;
let currentQR = null;

async function connectWhatsApp() {
    const { state, saveCreds } = await useMultiFileAuthState('auth_info');

    sock = makeWASocket({ auth: state });

    sock.ev.on('creds.update', saveCreds);

    sock.ev.on('connection.update', async ({ connection, qr }) => {
        if (qr) {
            currentQR = await qrcode.toDataURL(qr);
            isReady = false;
            console.log('QR Code nouveau — ouvrir: http://localhost:3000/qr');
        }

        if (connection === 'close') {
            isReady = false;
            currentQR = null;
            console.log('🔴 Déconnecté — Reconnexion dans 3s...');
            setTimeout(connectWhatsApp, 3000);
        }

        if (connection === 'open') {
            isReady = true;
            currentQR = null;
            console.log('🟢 WhatsApp connecté!');
        }
    });
}

connectWhatsApp();

// ══════════════════════════════
// Page QR Code
// ══════════════════════════════
app.get('/qr', (req, res) => {
    if (isReady) {
        return res.send(`
        <!DOCTYPE html>
        <html dir="ltr">
        <head>
            <meta charset="UTF-8">
            <title>WhatsApp - Connecté</title>
            <style>
                body { font-family: Arial; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; background: #f0f0f0; }
                .box { background: white; padding: 40px; border-radius: 20px; text-align: center; box-shadow: 0 4px 20px rgba(0,0,0,0.1); }
                .icon { font-size: 60px; }
                h2 { color: #25D366; }
                p { color: #666; }
            </style>
        </head>
        <body>
            <div class="box">
                <div class="icon">✅</div>
                <h2>WhatsApp connecté !</h2>
                <p>Le serveur fonctionne correctement.</p>
            </div>
        </body>
        </html>
        `);
    }

    if (!currentQR) {
        return res.send(`
        <!DOCTYPE html>
        <html dir="ltr">
        <head>
            <meta charset="UTF-8">
            <title>WhatsApp - Chargement</title>
            <meta http-equiv="refresh" content="3">
            <style>
                body { font-family: Arial; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; background: #f0f0f0; }
                .box { background: white; padding: 40px; border-radius: 20px; text-align: center; box-shadow: 0 4px 20px rgba(0,0,0,0.1); }
                .spinner { width: 50px; height: 50px; border: 5px solid #f3f3f3; border-top: 5px solid #25D366; border-radius: 50%; animation: spin 1s linear infinite; margin: 20px auto; }
                @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
                h2 { color: #333; }
                p { color: #666; }
            </style>
        </head>
        <body>
            <div class="box">
                <div class="spinner"></div>
                <h2>⏳ Chargement en cours...</h2>
                <p>La page se rafraîchit automatiquement.</p>
            </div>
        </body>
        </html>
        `);
    }

    res.send(`
    <!DOCTYPE html>
    <html dir="ltr">
    <head>
        <meta charset="UTF-8">
        <title>WhatsApp - Scanner le QR</title>
        <meta http-equiv="refresh" content="30">
        <style>
            body { font-family: Arial; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; background: #f0f0f0; }
            .box { background: white; padding: 40px; border-radius: 20px; text-align: center; box-shadow: 0 4px 20px rgba(0,0,0,0.1); max-width: 400px; }
            img { width: 280px; height: 280px; border: 2px solid #eee; border-radius: 10px; }
            h2 { color: #333; margin-bottom: 5px; }
            p { color: #666; font-size: 14px; }
            .steps { text-align: left; background: #f9f9f9; padding: 15px; border-radius: 10px; margin-top: 15px; }
            .steps p { margin: 5px 0; color: #444; }
            .badge { background: #25D366; color: white; padding: 5px 15px; border-radius: 20px; font-size: 12px; display: inline-block; margin-bottom: 15px; }
            .timer { margin-top: 15px; color: #999; font-size: 12px; }
        </style>
    </head>
    <body>
        <div class="box">
            <div class="badge">🔗 Connexion WhatsApp</div>
            <h2>Scanner le QR Code</h2>
            <img src="${currentQR}" alt="QR Code"/>
            <div class="steps">
                <p>1️⃣ Ouvrez WhatsApp sur votre téléphone</p>
                <p>2️⃣ Allez dans Paramètres → Appareils connectés</p>
                <p>3️⃣ Appuyez sur "Connecter un appareil"</p>
                <p>4️⃣ Scannez ce QR Code 📷</p>
            </div>
            <p class="timer">⏱ La page se rafraîchit toutes les 30 secondes</p>
        </div>
    </body>
    </html>
    `);
});

// ══════════════════════════════
// API Routes
// ══════════════════════════════

// Envoyer un message
app.post('/send', async (req, res) => {
    const { phone, message } = req.body;
    if (!isReady) return res.status(503).json({ success: false, error: 'WhatsApp non connecté' });
    if (!phone || !message) return res.status(400).json({ success: false, error: 'phone et message requis' });
    try {
        const formatted = phone.startsWith('212') ? phone : '212' + phone.replace(/^0/, '');
        await sock.sendMessage(formatted + '@s.whatsapp.net', { text: message });
        console.log(`✅ Message envoyé à ${formatted}`);
        res.json({ success: true, phone: formatted });
    } catch (err) {
        console.error('❌ Erreur:', err.message);
        res.status(500).json({ success: false, error: err.message });
    }
});

// Envoyer à plusieurs
app.post('/send-bulk', async (req, res) => {
    const { contacts } = req.body;
    if (!isReady) return res.status(503).json({ success: false, error: 'WhatsApp non connecté' });
    if (!Array.isArray(contacts) || contacts.length === 0) return res.status(400).json({ success: false, error: 'contacts requis' });
    const results = [];
    for (let i = 0; i < contacts.length; i++) {
        const { phone, message } = contacts[i];
        try {
            const formatted = phone.startsWith('212') ? phone : '212' + phone.replace(/^0/, '');
            await sock.sendMessage(formatted + '@s.whatsapp.net', { text: message });
            results.push({ phone: formatted, success: true });
            console.log(`✅ [${i+1}/${contacts.length}] Envoyé à ${formatted}`);
            if (i < contacts.length - 1) await new Promise(r => setTimeout(r, 1500));
        } catch (err) {
            results.push({ phone, success: false, error: err.message });
        }
    }
    res.json({ success: true, results });
});

// Déconnecter WhatsApp
app.post('/disconnect', async (req, res) => {
    try {
        isReady = false;
        currentQR = null;
        if (sock) {
            sock.ev.removeAllListeners();
            await sock.logout().catch(() => {});
            sock.end().catch(() => {});
        }
        fs.rmSync('./auth_info', { recursive: true, force: true });
        console.log('🔴 WhatsApp déconnecté.');
        res.json({ success: true });
        setTimeout(connectWhatsApp, 2000);
    } catch (err) {
        console.error('❌ Erreur:', err.message);
        res.status(500).json({ success: false, error: err.message });
        setTimeout(connectWhatsApp, 2000);
    }
});

// Statut du serveur
app.get('/status', (req, res) => {
    res.json({ ready: isReady });
});

app.listen(3000, () => {
    console.log('🚀 Serveur démarré sur le port 3000');
    console.log('📱 QR Code: http://localhost:3000/qr');
});