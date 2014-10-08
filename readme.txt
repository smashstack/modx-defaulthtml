# Add GA code in (copy it from their site) at launch

# Handy terminal commands
-- sass --watch stylesheet.scss:stylesheet.css --style compressed

# Update file path on both dev-index.html and .gitignore (for SASS Cache directory)

# HTML Setup Documentation: http://screencast.com/t/r17bvTMT4m

=========================================
** Add these .htaccess rules 
=========================================

# compress text, html, javascript, css, xml:
AddOutputFilterByType DEFLATE text/plain
AddOutputFilterByType DEFLATE text/html
AddOutputFilterByType DEFLATE text/xml
AddOutputFilterByType DEFLATE text/css
AddOutputFilterByType DEFLATE application/xml
AddOutputFilterByType DEFLATE application/xhtml+xml
AddOutputFilterByType DEFLATE application/rss+xml
AddOutputFilterByType DEFLATE application/javascript
AddOutputFilterByType DEFLATE application/x-javascript
 
## EXPIRES CACHING ##
 
ExpiresActive On
ExpiresByType image/jpg "access plus 1 year"
ExpiresByType image/jpeg "access plus 1 year"
ExpiresByType image/gif "access plus 1 year"
ExpiresByType image/png "access plus 1 year"
ExpiresByType text/css "access plus 1 month"
ExpiresByType application/pdf "access plus 1 month"
ExpiresByType text/x-javascript "access plus 1 month"
ExpiresByType application/x-shockwave-flash "access plus 1 month"
ExpiresByType image/x-icon "access plus 1 year"
ExpiresDefault "access plus 2 days"
 
<FilesMatch "\.(htm|html|php)$">
    <IfModule mod_headers.c>
        BrowserMatch MSIE ie
        Header set X-UA-Compatible "IE=Edge,chrome=1" env=ie
    </IfModule>
</FilesMatch>

AddType 'text/html; charset=UTF-8' html