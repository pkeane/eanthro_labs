{extends file="layout.tpl"}
{block name="title"}DASe Login{/block} 

{block name="content"}
<div class="list" id="browse">
	{if $msg}<h3 class="alert">{$msg}</h3>{/if}
	<h1>Please Login to Dase:</h1>
	<form id="loginForm" action="login" method="post">
		<p>
		<label for="username-input">username:</label>
		<input type="text" id="username-input" name="username">
		<input type="hidden" value="{$target}" name="target">
		</p>
		<p>
		<label for="password-input">password:</label>
		<input type="password" id="password-input" name="password">
		</p>
		<p>
		<input type="submit" value="login">
		</p>
	</form>
</div>
{/block}
