<?php
  require_once 'db_connect.php';

  if (!isset($_GET['id'])) {
      header('Location: index.php');
      exit;
  }

  $id = $_GET['id'];

  // actualizar post
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $contenido = $_POST['writing'];
      
      $sql = "UPDATE posts SET content = :content WHERE id = :id";
      $stmt = $pdo->prepare($sql);
      $stmt->execute(['content' => $contenido, 'id' => $id]);
      
      header('Location: index.php');
      exit;
  }

  // cargar el post actual
  $sql = "SELECT * FROM posts WHERE id = :id";
  $stmt = $pdo->prepare($sql);
  $stmt->execute(['id' => $id]);
  $post = $stmt->fetch(PDO::FETCH_ASSOC);

  if (!$post) {
      header('Location: index.php');
      exit;
  }
?>

<?php include 'header.php' ?>

  <main>
    <section class="writing_area">
      <form action="edit.php?id=<?php echo $id; ?>" method="post">
        <label for="writing"><h3>Editar entrada</h3></label>
        <textarea name="writing" id="writing" required><?php echo htmlspecialchars($post['content']); ?></textarea>
        <button type="submit">Guardar cambios</button>
        <a href="index.php"><button type="button">Cancelar</button></a>
      </form>
    </section>
  </main>

<?php include 'footer.php' ?>