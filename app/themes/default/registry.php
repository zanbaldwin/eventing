<?php foreach($packages as $package): ?>

  <section>
    <header>
      <hgroup>
        <h1><?php echo a($package->url, $package->name); ?></h1>
        <h2><?php echo $package->description; ?></h2>
      </hgroup>
    </header>
    <p>Author: <?php echo $package->author->name; ?> (<?php echo '<a href="mailto:'.htmlentities($package->author->email).'">' . htmlentities($package->author->email) . '</a>'; ?>).</p>
    <p>
      Versions:
      <ul>
      <?php foreach($package->versions as $v => $u): ?>
        <li><?php echo a($u, $v); ?></li>
      <?php endforeach; ?>
      </ul>
    </p>
  </section>

<?php endforeach; ?>