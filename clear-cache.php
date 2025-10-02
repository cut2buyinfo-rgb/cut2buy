<?php

// OPcache রিসেট করার ফাংশন
if (function_exists('opcache_reset')) {
    opcache_reset();
    echo "OPcache has been successfully reset!";
} else {
    echo "OPcache is not enabled or the opcache_reset function is not available.";
}

?>