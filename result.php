<?php
  
  require_once 'db_connect.php';

  $search = isset($_GET['search']) ? trim($_GET['search']) : '';
  $posts = [];

  if (!empty($search)) {
    try {
      // Buscar en el contenido de los posts
      $sql = "SELECT id, content, publish_date 
              FROM posts 
              WHERE content LIKE :search 
              ORDER BY publish_date DESC";
      $stmt = $pdo->prepare($sql);
      $stmt->execute(['search' => '%' . $search . '%']);
      $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
      die("Error en la búsqueda: " . $e->getMessage());
    }
  }
?>

<?php include 'header.php' ?>

  <main>
    <div class="search_container">
      <form action="result.php" method="get">
        <input type="search" name="search" id="search" value="<?php echo htmlspecialchars($search); ?>">
        <button type="submit">
          <img src="./svgs/search.svg" alt="Buscar post">
        </button>
      </form>
    </div>

    <!-- resultados -->
    <section class="contents_list">
      <?php if (empty($search)): ?>
        <h3>Escribe algo para buscar</h3>
        <p><a href="index.php">← Volver al inicio</a></p>
      <?php elseif (empty($posts)): ?>
        <h3>No se encontraron resultados para "<?php echo htmlspecialchars($search); ?>"</h3>
        <p><a href="index.php">← Volver al inicio</a></p>
      <?php else: ?>
        <h3>Resultados para "<?php echo htmlspecialchars($search); ?>" (<?php echo count($posts); ?>)</h3>
        <p><a href="index.php">← Volver al inicio</a></p>
        
        <?php foreach ($posts as $post): ?>
          <article class="post_preview">

            <div class="post_timestamp">
              <?php echo date('d/m/Y H:i:s', strtotime($post['publish_date'])); ?>
            </div>

            <div class="post_content">
              <?php 
                // Resaltar el término de búsqueda
                $content = htmlspecialchars($post['content']);
                $highlighted = str_ireplace(
                  htmlspecialchars($search), 
                  '<mark>' . htmlspecialchars($search) . '</mark>', 
                  $content
                );
                echo nl2br($highlighted);
              ?>
            </div>
            

            <div class="post_actions">
              <a href="edit.php?id=<?php echo $post['id']; ?>">
                <button type="button">Editar</button>
              </a>
              <a href="index.php?eliminar=<?php echo $post['id']; ?>" 
                 onclick="return confirm('¿Estás seguro de eliminar este post?')">
                <button type="button" class="btn-eliminar">Eliminar</button>
              </a>
            </div>
          </article>
        <?php endforeach; ?>
      <?php endif; ?>
    </section>
  </main>

<?php include 'footer.php' ?>