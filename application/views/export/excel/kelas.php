<!DOCTYPE html>
<html>

<head>
  <title>Export Data Ke Excel Dengan PHP - www.malasngoding.com</title>
</head>

<body>
  <style type="text/css">
    body {
      font-family: sans-serif;
    }

    table {
      margin: 50px auto;
      border-collapse: collapse;
    }

    table th,
    table td {
      border: 1px solid #3c3c3c;
      padding: 3px 8px;

    }

    a {
      background: blue;
      color: #fff;
      padding: 8px 10px;
      text-decoration: none;
      border-radius: 2px;
    }
  </style>

  <?php
  header("Content-type: application/vnd-ms-excel");
  header("Content-Disposition: attachment; filename=Data Kelas.xls");
  ?>

  <center>
    <h4>Data Kelas <br />PP Putra Taruna Al-Qur'an</h4>
  </center>

  <table border="1">
    <tr>
      <th style="width: 50px;">No</th>
      <th style="width: 120px;">Nama Kelas</th>
      <th style="width: 180px;">Pembimbing</th>
      <th style="width: 100px;">Santri (L)</th>
      <th style="width: 100px;">Santri (P)</th>
      <th style="width: 100px;">Total Santri</th>
      <th style="width: 150px;">Lokasi/Ruangan</th>
    </tr>
    <?php
    $no = 1;
    foreach ($kelas as $kls) : ?>
      <tr>
        <td><?= $no++; ?></td>
        <td><?= $kls['NamaKelas']; ?></td>
        <td><?= !empty($kls['NamaMusyrif']) ? $kls['NamaMusyrif'] : '-'; ?></td>
        <td><?= (int) $kls['JumlahLaki']; ?></td>
        <td><?= (int) $kls['JumlahPerempuan']; ?></td>
        <td><?= $kls['JumlahSantri']; ?></td>
        <td><?= !empty($kls['Lokasi']) ? $kls['Lokasi'] : '-'; ?></td>
      </tr>
    <?php endforeach; ?>
  </table>
</body>

</html>