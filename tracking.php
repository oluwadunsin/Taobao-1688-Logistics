<?php require_once('header.php'); ?>

<div class="containerIframe">
  <iframe class="responsive-iframe" src="track"></iframe>
</div>
<style>
    .containerIframe {
      position: relative;
      overflow: hidden;
      width: 100%;
      padding-top: 56.25%; /* 16:9 Aspect Ratio (divide 9 by 16 = 0.5625) */
    }

    /* Then style the iframe to fit in the container div with full height and width */
    .responsive-iframe {
      position: absolute;
      top: 0;
      left: 0;
      bottom: 0;
      right: 0;
      width: 100%;
      height: 100%;
    }
</style>

<?php require_once('footer.php'); ?>