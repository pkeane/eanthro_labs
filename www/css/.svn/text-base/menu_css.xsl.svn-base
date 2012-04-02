<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
  <xsl:output method="text"/>
  <xsl:strip-space elements="*"/>
  <xsl:variable name="borderLevel">1</xsl:variable>

  <xsl:template match="/">
/* this file is generated DO NOT EDIT */

ul#menu a {
display:block;
padding: 4px 7px;
margin: 2px 0px;
}
ul#menu  li  {
/* fixes ie problem */
display:inline;
}
ul#menu  li.hide  {
display:none;
}
ul#menu  li  ul {
margin-left:18px;
}
ul#menu  li ul li ul {
padding: 3px 6px;
margin-left:0px;
background-color: #eee;
}
ul#menu  input {
margin: 2px 0;
}
ul#menu li ul li a{
padding:3px 6px;
margin: 0px;
border-top: 1px solid #fff;
}
	<xsl:apply-templates/>
  </xsl:template>

  <xsl:template match="hue">
#menu li#<xsl:value-of select="@section"/>-menu a.main { border: 1px solid #<xsl:value-of select="hex[@level='1']"/>; border-left: 18px solid #<xsl:value-of select="hex[@level='1']"/>; background-color: #ffffff; }
#menu li#<xsl:value-of select="@section"/>-menu a:hover , a:active { background-color: #<xsl:value-of select="hex[@level='4']"/>; }
#menu ul#<xsl:value-of select="@section"/>-submenu { background-color: #<xsl:value-of select="hex[@level='4']"/>; }
#menu ul#<xsl:value-of select="@section"/>-submenu a { background-color: #<xsl:value-of select="hex[@level='4']"/>; }
#menu ul#<xsl:value-of select="@section"/>-submenu a:hover { background-color: #<xsl:value-of select="hex[@level='5']"/>; }

  </xsl:template>

</xsl:stylesheet>
