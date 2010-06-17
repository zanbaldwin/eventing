<?php
    // Load some data from a model.
    $data = $this->load->model('default')
          ? $this->model('default')->dummy_data()
          : 'Unable to load dummy data.';
