<?php
  error_reporting(E_ALL);
  ini_set('display_errors', 1);
  
  require_once 'db_connect.php';

  // guardar nuevo post
  if (isset($_POST['enviar'])) {
    $contenido = trim($_POST['writing']);
    
    if (!empty($contenido)) {
      try {
        $sql = "INSERT INTO posts (content) VALUES (:content)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['content' => $contenido]);
        
        header('Location: index.php');
        exit;
      } catch(PDOException $e) {
        die("Error al guardar: " . $e->getMessage());
      }
    }
  }

  // eliminar
  if (isset($_GET['eliminar'])) {
    $id = $_GET['eliminar'];
    
    try {
      $sql = "DELETE FROM posts WHERE id = :id";
      $stmt = $pdo->prepare($sql);
      $stmt->execute(['id' => $id]);
      
      header('Location: index.php');
      exit;
    } catch(PDOException $e) {
      die("Error al eliminar: " . $e->getMessage());
    }
  }

  // cargar posts anteriores
  try {
    $sql = "SELECT id, content, publish_date FROM posts ORDER BY publish_date DESC";
    $stmt = $pdo->query($sql);
    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
  } catch(PDOException $e) {
    die("Error al cargar posts: " . $e->getMessage());
  }
?>

<?php include 'header.php' ?>

  <main>
    <!-- buscar -->
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
        <textarea name="writing" id="writing" placeholder="Escribe aquí.." required></textarea>
        <button type="submit" name="enviar">Enviar</button>
      </form>
    </section>

    <!-- lista de entradas anteriores -->
    <section class="contents_list">
      <h3>Entrada anterior</h3>
      <?php if (empty($posts)): ?>
        <p>No hay posts todavía. ¡Escribe el primero!</p>
      <?php else: ?>
        <?php foreach ($posts as $post): ?>
          <article class="post_preview">
            
            <div class="post_timestamp">
              <?php echo date('d/m/Y H:i:s', strtotime($post['publish_date'])); ?>
            </div>
            
            <div class="post_content">
              <?php 
                $preview = mb_substr($post['content'], 0, 150);
                if (mb_strlen($post['content']) > 150) {
                    $preview .= '...';
                }
                echo nl2br(htmlspecialchars($preview)); 
              ?>
            </div>
          
            <div class="post_actions">
              <a href="edit.php?id=<?php echo $post['id']; ?>"><button type="button" class="btn-editar">Editar</button></a>
              <a href="?eliminar=<?php echo $post['id']; ?>" 
                 onclick="return confirm('Estás seguro?')"><button type="button" class="btn-eliminar">Eliminar</button></a>
            </div>
          </article>
        <?php endforeach; ?>
      <?php endif; ?>
    </section>
  </main>

  <?php include 'footer.php' ?>