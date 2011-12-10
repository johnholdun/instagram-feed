<?php

function page_path($page) {
  global $root;
  if ($page <= 1) {
    return $root;
  } else {
    return "$root/page/$page";
  }
}