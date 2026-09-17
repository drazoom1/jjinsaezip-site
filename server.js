// 찐새집 홈페이지 — 정적 파일(site/)을 Express로 서빙. Hostinger Node.js 배포용.
const express = require('express');
const path = require('path');
const app = express();
const PORT = process.env.PORT || 3000;
app.use(express.static(path.join(__dirname, 'site'), { maxAge: '1h', extensions: ['html'] }));
app.get('*', (_req, res) => res.sendFile(path.join(__dirname, 'site', 'index.html')));
app.listen(PORT, () => console.log(`jjinsaezip site on :${PORT}`));
