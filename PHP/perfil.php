<?php
require 'auth_guard.php'; 
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Formulario de Perfil</title>
  <link rel="stylesheet" href="../CSS/styles_perfil.css">
  <style>
    .left-panel .photo-box {
      background-color: #444; 
      color: white;
      padding: 20px;
      text-align: center;
      cursor: pointer; 
      border-radius: 5px;
      margin-bottom: 20px;
    }

    .left-panel input[type="file"] {
      display: none; 
    }
  </style>
</head>
<body>
  <header class="nav">
    <a href="../index.html">
      <img src="../images/imagesolologo.png" class="nav-logo" alt="Logo">
    </a>

    <nav> 
      <a href="../index.html">Inicio</a>
      
    </nav>
  </header>

  <main class="form-container">
    <div class="left-panel">
      <label for="photo-upload" class="photo-box">Foto</label>
      <input type="file" id="photo-upload" accept=".jpg, .jpeg, .png">

      <label>Nombre: <input type="text"></label>
      <label>Edad: <input type="number"></label>
      <label>Cédula: <input type="text"></label>
      <label>Estado Civil: <input type="text"></label>
      <label>Teléfono: <input type="tel"></label>
      <label>Dirección: <input type="text"></label>
      <label>Empleo al que quiere aplicar:
        <select>
          <option>Seleccione</option>
          <option>Diseñador</option>
          <option>Programador</option>
        </select>
      </label>
    </div>

    <div class="right-panel">
      <label>Descripción General (Estudios - experiencia):</label>
      <textarea rows="10"></textarea>
      <div class="upload-section">
        <label for="cv-upload" class="upload-label">SUBIR HOJA DE VIDA Y DIPLOMAS (PDF) ⬆</label>
        <input type="file" id="cv-upload" hidden>
      </div>
      <button class="submit-btn">ENVIAR</button>
    </div>
  </main>
</body>
</html>
