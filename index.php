<?php
$hasil = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $angka1 = $_POST["angka1"] ?? "";
    $angka2 = $_POST["angka2"] ?? "";
    $operator = $_POST["operator"] ?? "";

    if (
        !is_numeric($angka1) ||
        !is_numeric($angka2) ||
        !in_array($operator, ["+", "-", "*", "/"])
    ) {
        $error = "Input tidak valid";
    } else {
        $a = (float) $angka1;
        $b = (float) $angka2;

        switch ($operator) {
            case "+":
                $hasil = $a + $b;
                break;
            case "-":
                $hasil = $a - $b;
                break;
            case "*":
                $hasil = $a * $b;
                break;
            case "/":
                if ($b == 0) {
                    $error = "Tidak bisa dibagi nol";
                } else {
                    $hasil = $a / $b;
                }
                break;
        }

        if ($error === "" && is_numeric($hasil)) {
            $hasil = rtrim(rtrim(sprintf("%.10f", $hasil), "0"), ".");
            if ($hasil === "-0" || $hasil === "") {
                $hasil = "0";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Kalkulator</title>

<style>
* {
    box-sizing: border-box;
}

body {
    margin: 0;
    min-height: 100vh;
    background: #101114;
    color: white;
    font-family: Arial, sans-serif;
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 15px;
}

.kalkulator {
    width: 100%;
    max-width: 360px;
    background: #1b1c20;
    padding: 22px;
    border-radius: 28px;
    box-shadow: 0 15px 40px #0008;
}

.judul {
    color: #aaa;
    font-size: 14px;
    letter-spacing: 3px;
    text-align: center;
    margin-bottom: 18px;
}

.layar {
    min-height: 145px;
    display: flex;
    flex-direction: column;
    justify-content: flex-end;
    align-items: flex-end;
    padding: 15px 5px;
    overflow: hidden;
}

.ekspresi {
    color: #aaa;
    font-size: 20px;
    min-height: 28px;
    overflow-wrap: anywhere;
    text-align: right;
}

.nilai {
    font-size: clamp(35px, 10vw, 48px);
    font-weight: 300;
    margin-top: 10px;
    overflow-wrap: anywhere;
    text-align: right;
    width: 100%;
}

.tombol {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 10px;
}

button {
    height: 62px;
    border: none;
    border-radius: 50%;
    background: #303137;
    color: white;
    font-size: 23px;
    cursor: pointer;
    -webkit-tap-highlight-color: transparent;
}

button:active {
    filter: brightness(1.4);
    transform: scale(.96);
}

.fungsi {
    background: #a5a6ad;
    color: #151515;
}

.operator {
    background: #ff9f0a;
    color: white;
}

.nol {
    grid-column: span 2;
    border-radius: 35px;
    text-align: left;
    padding-left: 27px;
}

.status {
    color: #ff7777;
    font-size: 13px;
    min-height: 18px;
    text-align: right;
    margin-bottom: 8px;
}

.catatan {
    color: #777;
    font-size: 11px;
    text-align: center;
    margin-top: 18px;
}
</style>
</head>

<body>
<div class="kalkulator">
    <div class="judul">KALKULATOR</div>

    <div class="layar">
        <div class="ekspresi" id="ekspresi">Siap menghitung</div>
        <div class="nilai" id="layar">0</div>
    </div>

    <div class="status" id="status">
        <?= htmlspecialchars($error) ?>
    </div>

    <div class="tombol">
        <button class="fungsi" onclick="bersihkan()">AC</button>
        <button class="fungsi" onclick="hapus()">⌫</button>
        <button class="fungsi" onclick="persen()">%</button>
        <button class="operator" onclick="pilihOperator('/')">÷</button>

        <button onclick="masuk('7')">7</button>
        <button onclick="masuk('8')">8</button>
        <button onclick="masuk('9')">9</button>
        <button class="operator" onclick="pilihOperator('*')">×</button>

        <button onclick="masuk('4')">4</button>
        <button onclick="masuk('5')">5</button>
        <button onclick="masuk('6')">6</button>
        <button class="operator" onclick="pilihOperator('-')">−</button>

        <button onclick="masuk('1')">1</button>
        <button onclick="masuk('2')">2</button>
        <button onclick="masuk('3')">3</button>
        <button class="operator" onclick="pilihOperator('+')">+</button>

        <button class="fungsi" onclick="ubahTanda()">+/−</button>
        <button class="nol" onclick="masuk('0')">0</button>
        <button onclick="masuk('.')">.</button>
        <button class="operator" onclick="hitung()">=</button>
    </div>

    <div class="catatan">PHP • KALKULATOR DIGITAL</div>
</div>

<script>
let angka = "0";
let angkaSebelum = null;
let operator = null;
let mulaiAngkaBaru = false;
let selesai = false;

const layar = document.getElementById("layar");
const ekspresi = document.getElementById("ekspresi");
const statusEl = document.getElementById("status");

function tampil() {
    layar.textContent = angka;
}

function masuk(nilai) {
    statusEl.textContent = "";
    if (selesai) {
        angka = "0";
        angkaSebelum = null;
        operator = null;
        selesai = false;
        ekspresi.textContent = "";
    }

    if (mulaiAngkaBaru) {
        angka = nilai === "." ? "0." : nilai;
        mulaiAngkaBaru = false;
    } else if (nilai === ".") {
        if (!angka.includes(".")) angka += ".";
    } else {
        angka = angka === "0" ? nilai : angka + nilai;
    }

    tampil();
}

function pilihOperator(op) {
    statusEl.textContent = "";
    if (operator !== null && !mulaiAngkaBaru) {
        hitungLanjut();
    }

    angkaSebelum = Number(angka);
    operator = op;
    mulaiAngkaBaru = true;
    selesai = false;

    const simbol = {"+": "+", "-": "−", "*": "×", "/": "÷"};
    ekspresi.textContent = angka + " " + simbol[op];
}

function hitungLanjut() {
    const a = angkaSebelum;
    const b = Number(angka);
    let hasil;

    if (operator === "+") hasil = a + b;
    if (operator === "-") hasil = a - b;
    if (operator === "*") hasil = a * b;
    if (operator === "/") {
        if (b === 0) {
            statusEl.textContent = "Tidak bisa dibagi nol";
            return false;
        }
        hasil = a / b;
    }

    if (!Number.isFinite(hasil)) {
        statusEl.textContent = "Hasil tidak valid";
        return false;
    }

    angka = String(Number(hasil.toPrecision(10)));
    tampil();
    return true;
}

function hitung() {
    if (operator === null || mulaiAngkaBaru) return;

    const simbol = {"+": "+", "-": "−", "*": "×", "/": "÷"};
    const teks = angkaSebelum + " " + simbol[operator] + " " + angka;

    if (hitungLanjut()) {
        ekspresi.textContent = teks + " =";
        angkaSebelum = null;
        operator = null;
        mulaiAngkaBaru = true;
        selesai = true;
    }
}

function bersihkan() {
    angka = "0";
    angkaSebelum = null;
    operator = null;
    mulaiAngkaBaru = false;
    selesai = false;
    ekspresi.textContent = "Siap menghitung";
    statusEl.textContent = "";
    tampil();
}

function hapus() {
    statusEl.textContent = "";
    if (mulaiAngkaBaru || selesai) {
        angka = "0";
        mulaiAngkaBaru = false;
        selesai = false;
    } else {
        angka = angka.length > 1 ? angka.slice(0, -1) : "0";
        if (angka === "-") angka = "0";
    }
    tampil();
}

function ubahTanda() {
    if (angka !== "0") {
        angka = String(-Number(angka));
        tampil();
    }
}

function persen() {
    angka = String(Number(angka) / 100);
    tampil();
}

document.addEventListener("keydown", function(e) {
    if (/^[0-9.]$/.test(e.key)) masuk(e.key);
    else if (["+", "-", "*", "/"].includes(e.key)) pilihOperator(e.key);
    else if (e.key === "Enter" || e.key === "=") hitung();
    else if (e.key === "Backspace") hapus();
    else if (e.key === "Escape") bersihkan();
});
</script>
</body>
</html>