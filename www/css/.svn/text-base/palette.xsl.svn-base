<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:output method="html"/>
	<xsl:template match="/">
		<html>
			<head>
				<title><xsl:text>Color Palette</xsl:text></title>
				<style type="text/css">
					div.color {
					border: 1px solid #666;
					-moz-border-radius: 5px;
					text-align: center;
					padding-top: 40px;
					width: 100px;
					height: 60px;
					}
				</style>
			</head>
			<body>
				<table class="{@id}">
					<xsl:apply-templates/>
				</table>
			</body>
		</html>
	</xsl:template>

	<xsl:template match="hue">
		<tr>
	<th><xsl:value-of select="@id"/>:<xsl:value-of select="@section"/></th>
			<xsl:apply-templates/>
		</tr>
	</xsl:template>

	<xsl:template match="hex">
		<td> 
			<div class="color" style="background-color:#{text()}">
			<xsl:apply-templates/>
			</div>
		</td>
	</xsl:template>

</xsl:stylesheet>
