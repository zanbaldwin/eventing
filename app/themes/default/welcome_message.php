<div class="content">
    <h1>Eventing Framework</h1>
    <p>This is the Eventing PHP Framework. Source code can be found on <a href="http://github.com/mynameiszanders/eventing">GitHub.com</a>.</p>
    <p>Dummy text: <?php echo $text; ?></p>
</div>
<?php if(isset($examples) && is_array($examples)): ?>
    <div class="content">
        <h1>Eventing Framework Examples</h1>
        <p>
            <ul>
                <?php
                    foreach($examples as $example)
                    {
                        echo "<li>{$example}</li>\n";
                    }
                ?>
            </ul>
        </p>
    </div>
<?php endif; ?>
