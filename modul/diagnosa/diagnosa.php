<title>Diagnosa - Chirexs 1.0</title>
<script>
  let kond = [];

  function kondArr(val) {
    kond.push(val);
    document.getElementById('kondisi').innerHTML = kond;
    document.getElementById('kondisi').value = kond;
  }
</script>

<?php
switch ($_GET['act']) {

  default:
    if ($_POST['submit']) {
      $arcolor = array('#ffffff', '#cc66ff', '#019AFF', '#00CBFD', '#00FEFE', '#A4F804');
      date_default_timezone_set("Asia/Jakarta");
      $inptanggal = date('Y-m-d H:i:s');

      $arbobot = array('0', '0.2', '0.4', '0.6', '0.8', '1.0');
      $argejala = array();

      $konds = explode(",", $_POST['kondisi']);
      for ($i = 0; $i < count($konds); $i++) {
        $arkondisi = explode("_", $konds[$i]);
        if (strlen($konds[$i]) > 1) {
          $argejala += array($arkondisi[0] => $arkondisi[1]);
        }
      }

      $sqlkondisi = mysqli_query($conn, "SELECT * FROM kondisi order by id+0");
      while ($rkondisi = mysqli_fetch_array($sqlkondisi)) {
        $arkondisitext[$rkondisi['id']] = $rkondisi['kondisi'];
      }

      $sqlpkt = mysqli_query($conn, "SELECT * FROM penyakit order by kode_penyakit+0");
      while ($rpkt = mysqli_fetch_array($sqlpkt)) {
        $arpkt[$rpkt['kode_penyakit']] = $rpkt['nama_penyakit'];
        $ardpkt[$rpkt['kode_penyakit']] = $rpkt['det_penyakit'];
        $arspkt[$rpkt['kode_penyakit']] = $rpkt['srn_penyakit'];
        $argpkt[$rpkt['kode_penyakit']] = $rpkt['gambar'];
      }


      // -------- perhitungan certainty factor (CF) ---------
      // --------------------- START ------------------------
      $sqlpenyakit = mysqli_query($conn, "SELECT * FROM penyakit order by kode_penyakit");
      $arpenyakit = array();
      while ($rpenyakit = mysqli_fetch_array($sqlpenyakit)) {
        $cftotal_temp = 0;
        $cf = 0;
        $sqlgejala = mysqli_query($conn, "SELECT * FROM basis_pengetahuan where kode_penyakit=$rpenyakit[kode_penyakit]");
        $cflama = 0;
        while ($rgejala = mysqli_fetch_array($sqlgejala)) {
          $arkondisi = explode("_", $konds[0]);
          $gejala = $arkondisi[0];

          for ($i = 0; $i < count($konds); $i++) {
            $arkondisi = explode("_", $konds[$i]);
            $gejala = $arkondisi[0];
            if ($rgejala['kode_gejala'] == $gejala) {
              $cf = ($rgejala['mb'] - $rgejala['md']) * $arbobot[$arkondisi[1]];
              if (($cf >= 0) && ($cf * $cflama >= 0)) {
                $cflama = $cflama + ($cf * (1 - $cflama));
              }
              if ($cf * $cflama < 0) {
                $cflama = ($cflama + $cf) / (1 - min(abs($cflama), abs($cf)));
              }
              if (($cf < 0) && ($cf * $cflama >= 0)) {
                $cflama = $cflama + ($cf * (1 + $cflama));
              }
            }
          }
        }
        if ($cflama > 0) {
          $arpenyakit += array($rpenyakit['kode_penyakit'] => number_format($cflama, 4));
        }
      }

      arsort($arpenyakit);

      $inpgejala = serialize($argejala);
      $inppenyakit = serialize($arpenyakit);

      $np1 = 0;
      foreach ($arpenyakit as $key1 => $value1) {
        $np1++;
        $idpkt1[$np1] = $key1;
        $vlpkt1[$np1] = $value1;
      }

      try {
        mysqli_query($conn, "INSERT INTO hasil (
                  tanggal,
                  gejala,
                  penyakit,
                  hasil_id,
                  hasil_nilai
				  ) 
	        VALUES(
                '$inptanggal',
                '$inpgejala',
                '$inppenyakit',
                '$idpkt1[1]',
                '$vlpkt1[1]'
				)");
      } catch (Exception $ex) {
        print_r($ex->getMessage());
      }

      // --------------------- END -------------------------

      echo "<div class='content'>
	<h2 class='text text-primary'>Hasil Diagnosis &nbsp;&nbsp;<button id='print' onClick='window.print();' data-toggle='tooltip' data-placement='right' title='Klik tombol ini untuk mencetak hasil diagnosa'><i class='fa fa-print'></i> Cetak</button> </h2>
	          <hr><table class='table table-bordered table-striped diagnosa'> 
          <th width=8%>No</th>
          <th width=10%>Kode</th>
          <th>Gejala yang dialami (keluhan)</th>
          <th width=20%>Pilihan</th>
          </tr>";
      $ig = 0;
      foreach ($argejala as $key => $value) {
        $kondisi = $value;
        $ig++;
        $gejala = $key;
        $sql4 = mysqli_query($conn, "SELECT * FROM gejala where kode_gejala = '$key'");
        $r4 = mysqli_fetch_array($sql4);
        echo '<tr><td>' . $ig . '</td>';
        echo '<td>G' . str_pad($r4['kode_gejala'], 3, '0', STR_PAD_LEFT) . '</td>';
        echo '<td><span class="hasil text text-primary">' . $r4['nama_gejala'] . "</span></td>";
        echo '<td><span class="kondisipilih" style="color:' . $arcolor[$kondisi] . '">' . $arkondisitext[$kondisi] . "</span></td></tr>";
      }
      $np = 0;
      foreach ($arpenyakit as $key => $value) {
        $np++;
        $idpkt[$np] = $key;
        $nmpkt[$np] = $arpkt[$key];
        $vlpkt[$np] = $value;
      }
      if ($argpkt[$idpkt[1]]) {
        $gambar = 'gambar/penyakit/' . $argpkt[$idpkt[1]];
      } else {
        $gambar = 'gambar/noimage.png';
      }
      echo "</table><div class='well well-small'><img class='card-img-top img-bordered-sm' style='float:right; margin-left:15px;' src='" . $gambar . "' height=200><h3>Hasil Diagnosa</h3>";
      echo "<div class='callout callout-default'>Jenis penyakit yang diderita adalah <b><h3 class='text text-success'>" . $nmpkt[1] . "</b> / " . round($vlpkt[1], 2) . " % (" . $vlpkt[1] . ")<br></h3>";
      echo "</div></div><div class='box box-info box-solid'><div class='box-header with-border'><h3 class='box-title'>Detail</h3></div><div class='box-body'><h4>";
      echo $ardpkt[$idpkt[1]];
      echo "</h4></div></div>
          <div class='box box-warning box-solid'><div class='box-header with-border'><h3 class='box-title'>Saran</h3></div><div class='box-body'><h4>";
      echo $arspkt[$idpkt[1]];
      echo "</h4></div></div>
          <div class='box box-danger box-solid'><div class='box-header with-border'><h3 class='box-title'>Kemungkinan lain:</h3></div><div class='box-body'><h4>";
      for ($ipl = 2; $ipl < count($idpkt); $ipl++) {
        echo " <h4><i class='fa fa-caret-square-o-right'></i> " . $nmpkt[$ipl] . "</b> / " . round($vlpkt[$ipl], 2) . " % (" . $vlpkt[$ipl] . ")<br></h4>";
      }
      echo "</div></div>
		  </div>";
    } else {

      // bukan form submit

?>
      <h2 class='text text-primary'>Melakukan Diagnosa Penyakit Anjing Moggy</h2>
      <hr>
      <div class='alert alert-success alert-dismissible'>
        <button type='button' class='close' data-dismiss='alert' aria-hidden='true'><span aria-hidden="true">&times;</span></button>
        <h4><i class='icon fa fa-exclamation-triangle'></i>Perhatian !</h4>
        Silahkan memilih gejala sesuai dengan kondisi anjing anda, anda dapat memilih kepastian kondisi anjing dari pasti
        tidak sampai pasti ya, jika sudah tekan tombol Cek Hasil untuk melihat
        hasil.
      </div>
      <section>
        <div class="container container-diagnosa">
          <form method="POST" action="diagnosa" class="panel panel-success">
            <div class="panel-heading">Panel Diagnosa Penyakit</div>
            <div class="panel-body">
              <div class="step step-0 active">
                <button type="button" class="btn btn-success next-btn start-diagnosa">Mulai Diagnosa</button>
                <!-- <input type="submit" name="submit" value="Submit"> -->
              </div>
              <!-- </form> -->
              <?php
              $sql3 = mysqli_query($conn, "SELECT * FROM gejala order by kode_gejala");
              $i = 0;
              while ($r3 = mysqli_fetch_array($sql3)) {
                $i++;
              ?>
                <div class="step step-<?php echo $i; ?>">
                  <!-- <p><input placeholder="First name..." oninput="this.className = ''"></p>
                    <p><input placeholder="Last name..." oninput="this.className = ''"></p> -->
                  <!-- <div class="form-group"> -->
                  <p class="gejala">
                    <?php echo "Gejala " . $r3['kode_gejala']; ?>
                  </p>
                  <p class="gejala">
                    <?php echo $r3['pertanyaan']; ?>
                  </p>

                  <?php
                  $s = "select * from kondisi order by id";
                  $q = mysqli_query($conn, $s) or die($s);
                  $j = 0;
                  while ($rw = mysqli_fetch_array($q)) {
                    $j++;
                  ?>
                    <div style="display: inline;">
                      <button type="button" class="btn btn-default next-btn" name="kondisiVal" value="<?php echo $r3['kode_gejala'] . "_" . $rw['id']; ?>" onClick="kondArr('<?php echo $r3['kode_gejala'] . '_' . $rw['id']; ?>')">
                        <?php echo $rw['kondisi']; ?>
                      </button>
                    </div>
                  <?php
                  }
                  ?>
                </div>
              <?php
              }
              ?>
              <div class="step step-<?php echo $i + 1; ?>">
                <input type="hidden" name="kondisi" id="kondisi" value=""></input>
                <input type="submit" name="submit" class="submit-btn" value="Cek Hasil">
              </div>
            </div>
            <div class="panel-footer">Silahkan pilih berdasarkan gejala yang anda lihat.</div>
          </form>
        </div>
      </section>



<?php

      break;
    }
}
?>