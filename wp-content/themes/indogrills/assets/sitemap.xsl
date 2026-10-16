<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  xmlns:s="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">
<xsl:output method="html" encoding="UTF-8" indent="yes"/>
<xsl:template match="/">
<html><head><meta charset="utf-8"/><title>IndoGrills Sitemap</title>
<style>body{font:14px Arial,sans-serif;margin:0;color:#1d2733}header{background:#15385F;color:#fff;padding:22px 30px;font-size:20px;font-weight:700}header span{color:#FF9733}
main{padding:24px 30px}table{border-collapse:collapse;width:100%}th{background:#F3F6FA;color:#44546A;text-align:left;padding:9px 12px;font-size:12px;text-transform:uppercase}
td{padding:9px 12px;border-bottom:1px solid #E1E6ED}a{color:#15385F}</style></head>
<body><header>INDO<span>GRILLS</span> &#8211; XML Sitemap</header><main>
<p><xsl:value-of select="count(s:urlset/s:url)"/> URLs</p>
<table><tr><th>URL</th><th>Images</th><th>Last modified</th><th>Priority</th></tr>
<xsl:for-each select="s:urlset/s:url"><tr>
<td><a href="{s:loc}"><xsl:value-of select="s:loc"/></a></td>
<td><xsl:value-of select="count(image:image)"/></td>
<td><xsl:value-of select="substring(s:lastmod,1,10)"/></td>
<td><xsl:value-of select="s:priority"/></td></tr></xsl:for-each>
</table></main></body></html>
</xsl:template></xsl:stylesheet>
