<?php
  // guardar post
  if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['writing'])) {
      $content = trim($_POST['writing']);
      
      if (!empty($content)) {
          // crear 'posts' directory si no existe
          if (!file_exists('posts')) {
              mkdir('posts', 0777, true);
          }
          
          // nombrar posts con el timestamp
          // y guardar el contenido
          $timestamp = time();
          $filename = 'posts/post_' . $timestamp . '.txt';
          
          $data = $timestamp . '|' . $content;
          file_put_contents($filename, $data);
          
          header('Location: index.php');
          exit;
      }
  }

  // traer los archivos guardados
  function getPosts() {
      $posts = [];
      
      if (file_exists('posts')) {
          $files = glob('posts/post_*.txt');
          
          foreach ($files as $file) {
              $content = file_get_contents($file);
              $parts = explode('|', $content, 2);
              
              if (count($parts) === 2) {
                  $posts[] = [
                      'timestamp' => $parts[0],
                      'content' => $parts[1],
                      'filename' => basename($file)
                  ];
              }
          }
          
          // ordenar
          usort($posts, function($a, $b) {
              return $b['timestamp'] - $a['timestamp'];
          });
      }
      
      return $posts;
  }

  $posts = getPosts();
?>

<?php include 'header.php' ?>

  <main>
    <!-- search form -->
    <div class="search_container">
      <form action="result.php" method="get">
        <input type="search" name="search" id="search">
        <button type="submit">
          <img src="./svgs/search.svg" alt="Buscar post">
        </button>
      </form>
    </div>

    <!-- text area -->
    <section class="writing_area">
      <form action="index.php" method="post">
        <label for="writing"><h3>¿Qué pasó hoy?</h3></label>
        <textarea name="writing" id="writing" placeholder="Escribe aquí.."></textarea>
        <button type="submit">Enviar</button>
      </form>
    </section>

    <!-- entrada anterior -->
    <section class="contents_list">
      <h3>Entrada anterior</h3>
      <?php if (empty($posts)): ?>
        <p>No hay posts todavía. ¡Escribe el primero!</p>
      <?php else: ?>
        <?php foreach ($posts as $post): ?>
          <article class="post_preview">
            <div class="post_timestamp">
              <?php echo date('d/m/Y H:i:s', $post['timestamp']); ?>
            </div>
            <div class="post_content">
              <?php 
                // demostrar maximo 150 letras
                $preview = mb_substr($post['content'], 0, 150);
                if (mb_strlen($post['content']) > 150) {
                    $preview .= '...';
                }
                echo nl2br(htmlspecialchars($preview)); 
              ?>
            </div>
          </article>
        <?php endforeach; ?>
      <?php endif; ?>
    </section>
  </main>

  <?php include 'footer.php' ?>