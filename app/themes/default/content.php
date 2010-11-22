<section>
  <article>
    <header>
      <h3>Development Testing</h3>
      <?php
        $prowl = a('?prowl');
        echo a(
          '#',
          'Add a route',
          array(
            'onclick' => "var m=prompt();if(m){document.location='{$prowl}'+m;}return false;"
          )
        );
      ?>.
    </header>
    <table>
      <thead>
        <td>Eventing URI</td>
        <td>Translated URL</td>
        <td>Route</td>
      </thead>
      <tbody>
        <?php foreach($routes as $route): ?>
          <tr>
            <td><span>"</span><?php echo $route->euri; ?><span>"</span></td>
            <td><?php echo $route->url; ?></td>
            <td>
              <?php echo $route->route; ?>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </article>
</section>