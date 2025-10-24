<?php

$searchTerm = isset($_GET['search']) ? trim($_GET['search']) : '';

function searchPosts($searchTerm) {
        $results = [];
        
        if (empty($searchTerm) || !file_exists('posts')) {
            return $results;
        }
        
        $files = glob('posts/post_*.txt');
        
        foreach ($files as $file) {
            $content = file_get_contents($file);
            $parts = explode('|', $content, 2);
            
            if (count($parts) === 2) {
                // check si el contenido lleva el termo
                if (stripos($parts[1], $searchTerm) !== false) {
                    $results[] = [
                        'timestamp' => $parts[0],
                        'content' => $parts[1],
                        'filename' => basename($file)
                    ];
                }
            }
        }
        
        // ordenar resultados
        usort($results, function($a, $b) {
            return $b['timestamp'] - $a['timestamp'];
        });
        
        return $results;
    }

    $results = searchPosts($searchTerm);
?>

<?php include 'header.php' ?>

  <main>
    <div class="search_container">
      <form action="search_result.php" method="get">
        <input type="search" name="search" id="search" value="<?php echo htmlspecialchars($searchTerm); ?>" placeholder="Buscar...">
        <button type="submit">
          <img src="./svgs/search.svg" alt="Buscar post">
        </button>
      </form>
    </div>

    <section class="search_results">
      <a href="index.php" class="back_link">← Volver al inicio</a>
      
      <h3>Resultados para: "<?php echo htmlspecialchars($searchTerm); ?>"</h3>
      
      <?php if (empty($searchTerm)): ?>
        <p>Por favor, escribe algo para buscar.</p>
      <?php elseif (empty($results)): ?>
        <p>No se encontraron resultados para "<?php echo htmlspecialchars($searchTerm); ?>"</p>
      <?php else: ?>
        <p>Se encontraron <?php echo count($results); ?> resultado(s):</p>
        
        <?php foreach ($results as $post): ?>
          <article class="post_preview">
            <div class="post_timestamp">
              <?php echo date('d/m/Y H:i:s', $post['timestamp']); ?>
            </div>
            <div class="post_content">
              <?php 

                // emphasize the keyword
                $content = htmlspecialchars($post['content']);
                $highlightedContent = str_ireplace(
                    $searchTerm, 
                    '<mark>' . htmlspecialchars($searchTerm) . '</mark>', 
                    $content
                );
                
                // mostrar 200 letras  
                $preview = mb_substr($highlightedContent, 0, 200);
                if (mb_strlen($post['content']) > 200) {
                    $preview .= '...';
                }
                echo nl2br($preview); 

              ?>
            </div>
          </article>
        <?php endforeach; ?>
      <?php endif; ?>
    </section>
  </main>

<?php include 'footer.php' ?>