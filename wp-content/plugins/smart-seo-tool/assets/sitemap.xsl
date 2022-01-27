<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" 
                xmlns:html="http://www.w3.org/TR/REC-html40"
                xmlns:sitemap="http://www.sitemaps.org/schemas/sitemap/0.9"
                xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:output method="html" version="1.0" encoding="UTF-8" indent="yes" />
	<xsl:template match="/">
		<html xmlns="http://www.w3.org/1999/xhtml">
			<head>
				<title>XML Sitemap</title>
				<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
				<meta name="robots" content="noindex,follow" />
				<style type="text/css">
					body {
						font-family:"Lucida Grande","Lucida Sans Unicode",Tahoma,Verdana;
						font-size:13px;
						background-color: #f5f5f5;
						padding:0;
						margin:0;
					}
					
					#intro {
						background-color:#CFEBF7;
						border:1px #2580B2 solid;
						padding:5px 13px 5px 13px;
						margin:10px;
					}
					
					#intro p {
						line-height: 16.8667px;
					}
					#intro strong {
						font-weight:normal;
					}

					#header{
						padding:30px 0;
						text-align: center;
						background-color: #fff;
					}

					#header p{
						color: #999;
					}

					#content{
						padding-top: 30px;
						max-width: 860px;
						margin-left: auto;
						margin-right: auto;
					}

					#content table{
						width: 100%;
						margin-left: auto;
						margin-right: auto;
						border-collapse:collapse;
					}

					#content td a{
						display: block;
						max-width: 600px;
						word-break:break-all;
						color: #2384c6;
					}

					#content td a:hover{
						color: #195C8B;
					}

					td {
						font-size:12px;
						padding:10px 5px;
						border-bottom:1px solid #ddd;
						color: #666;
					}
					
					th {
						text-align:left;
						padding-right:30px;
						font-size:12px;
						border-bottom:1px solid #ddd;
					}

					tr:hover{
						background-color: #eee;
					}
					

					#footer {
						text-align: center;
						padding:40px 20px 30px 20px;
						margin-top:10px;
						font-size:8pt;
						color:gray;
					}
					

					a {
						color:#2384c6;
					}
				</style>
			</head>
			<body>
				<xsl:apply-templates></xsl:apply-templates>

			</body>
		</html>
	</xsl:template>
	
	
	<xsl:template match="sitemap:urlset">
        <div id="header">
			<h1>XML 网站地图</h1>
			<p>这是一个XML格式的网站地图文件，符合百度和谷歌等搜索引擎Sitemap协议。</p>
		</div>

		<div id="content">
			<table cellpadding="5">
				<tr style="border-bottom:1px black solid;">
					<th>URL地址</th>
					<th>优先级</th>
					<th>变更频率</th>
					<th>最后更新时间</th>
				</tr>
				<xsl:variable name="lower" select="'abcdefghijklmnopqrstuvwxyz'"/>
				<xsl:variable name="upper" select="'ABCDEFGHIJKLMNOPQRSTUVWXYZ'"/>
				<xsl:for-each select="./sitemap:url">
					<tr>
						<xsl:if test="position() mod 2 != 1">
							<xsl:attribute  name="class">high</xsl:attribute>
						</xsl:if>
						<td>
							<xsl:variable name="itemURL">
								<xsl:value-of select="sitemap:loc"/>
							</xsl:variable>
							<a href="{$itemURL}">
								<xsl:value-of select="sitemap:loc"/>
							</a>
						</td>
						<td>
							<xsl:value-of select="concat(sitemap:priority*100,'%')"/>
						</td>
						<td>
							<xsl:value-of select="concat(translate(substring(sitemap:changefreq, 1, 1),concat($lower, $upper),concat($upper, $lower)),substring(sitemap:changefreq, 2))"/>
						</td>
						<td>
							<xsl:value-of select="concat(substring(sitemap:lastmod,1,10),concat(' ', substring(sitemap:lastmod,12,8)))"/>
						</td>
					</tr>
				</xsl:for-each>
			</table>
		</div>

		<div id="footer">
			由<a href="https://www.wbolt.com/plugins/sst" target="_blank">Smart SEO Tool</a> 插件生成
		</div>
	</xsl:template>
	
	
	<xsl:template match="sitemap:sitemapindex">
		<div id="header">
			<h1>XML 网站地图</h1>
			<p>这是一个XML格式的网站地图文件，符合百度和谷歌等搜索引擎Sitemap协议。</p>
		</div>

		<div id="content">
			<table cellpadding="5">
				<tr style="border-bottom:1px black solid;">
					<th>子Sitemap地址</th>
					<th>最后更新时间</th>
				</tr>
				<xsl:for-each select="./sitemap:sitemap">
					<tr>
						<xsl:if test="position() mod 2 != 1">
							<xsl:attribute  name="class">high</xsl:attribute>
						</xsl:if>
						<td>
							<xsl:variable name="itemURL">
								<xsl:value-of select="sitemap:loc"/>
							</xsl:variable>
							<a href="{$itemURL}">
								<xsl:value-of select="sitemap:loc"/>
							</a>
						</td>
						<td>
							<xsl:value-of select="concat(substring(sitemap:lastmod,1,10),concat(' ', substring(sitemap:lastmod,12,8)))"/>
						</td>
					</tr>
				</xsl:for-each>
			</table>
		</div>

		<div id="footer">
			由<a href="https://www.wbolt.com/plugins/sst" target="_blank">Smart SEO Tool</a> 插件生成
		</div>
	</xsl:template>
</xsl:stylesheet>