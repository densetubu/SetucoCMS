$(function (){
	
	var forminput = $("form").find("input,textarea");
	var valCheck = true;
	forminput.blur(function (){
		var inputID = $(this).attr("id");
		switch(inputID){
			case "account_id":
			var accountID = $(this).val();
			var errmsg = "";
			$(this).parent("dd").find(".balErr").remove(".balErr");
			if(accountID == ""){
				errmsg += "<li>管理者アカウントのログインIDを入力してください。</li>";
			}
			
			if((accountID.length > 31 || accountID.length < 4)){
				errmsg += "<li>管理者アカウントのログインIDは4文字以上30文字以下で入力してください。</li>";
			}
			
			if(errmsg != ""){
				$(this).parent("dd").append("<span class='balErr'><ul>" + errmsg + "</ul></span>");
				$(this).parent("dd").find(".balErr").css({"visibility":"visible","opacity":0}).fadeTo("fast",1);
			}
			break;
			
			case "account_pass":
			var accountPass = $(this).val();
			var errmsg = "";
			$(this).parent("dd").find(".balErr").remove(".balErr");
			if(accountPass == ""){
				errmsg += "<li>接続ユーザーのパスワードを入力してください。</li>";
			}
			if((!accountPass.match(/^[!"#$%&\'()=~|\-^@\[;:\],.\/`{+*}<>?]+$/) && !accountPass.match(/^[0-9a-zA-Z]+$/) && accountPass != "")){
				errmsg += "<li>パスワードに使用できる文字は半角英数字[0-9][a-z][A-Z]と一部の半角記号[! &quot; # - $ % &amp; &#039; ( ) = ~ | ^ @ [ ; : ] , . / ` { + * } &lt; &gt; ?]のみです。</li>";				
			}
			if((accountPass.length > 31 || accountPass.length < 6) && accountPass.length != 0 && $(this).next(".balErr").length == 0){
				errmsg += "<li>パスワードは6文字以上30文字以下で入力してください。</li>";
			}
			

			if(errmsg != ""){
				$(this).parent("dd").append("<span class='balErr'><ul>" + errmsg + "</ul></span>");
				$(this).parent("dd").find(".balErr").css({"visibility":"visible","opacity":0}).fadeTo("fast",1);
			}
			
			break;
			
			case "site_name":
			var siteName = $(this).val();
			$(this).parent("dd").find(".balErr").remove();
			if(siteName == "" && $(this).next(".balErr").length == 0){
				$(this).parent("dd").append("<span class='balErr'>" + "サイト名を入力してください。" + "</span>");
			}
			if(siteName.length > 101 && $(this).next(".balErr").length == 0){
				$(this).parent("dd").append("<span class='balErr'>" + "サイト名は100文字以下で入力してください。" + "</span>");
			}
			break;
			
			case "site_url":
			var siteURL = $(this).val();
			$(this).parent("dd").find(".balErr").remove();
			if(siteURL == "" && $(this).next(".balErr").length == 0){
				$(this).parent("dd").append("<span class='balErr'>" + "サイトURLを入力してください。" + "</span>");
			}
			
			if((siteURL.length > 51 || siteURL.length < 6) && $(this).next(".balErr").length == 0){
				$(this).parent("dd").append("<span class='balErr'>" + "サイトURLは6文字以上50文字以下で入力してください。" + "</span>");
			}
			break;
						
			case "db_host":
			var dbHost = $(this).val();
			$(this).parent("dd").find(".balErr").remove();
			if(dbHost == "" && $(this).next(".balErr").length == 0){
				$(this).parent("dd").append("<span class='balErr'>" + "データベースサーバーホストを入力してください。" + "</span>");
			}
			break;
			
			case "db_name":
			var dbName = $(this).val();
			$(this).parent("dd").find(".balErr").remove();
			if(dbName == "" && $(this).next(".balErr").length == 0){
				$(this).parent("dd").append("<span class='balErr'>" + "データベース名を入力してください。" + "</span>");
			}
			break;
			
			case "db_user":
			var dbUser = $(this).val();
			$(this).parent("dd").find(".balErr").remove();
			if(dbUser == "" && $(this).next(".balErr").length == 0){
				$(this).parent("dd").append("<span class='balErr'>" + "ユーザー名を入力してください。" + "</span>");
			}
			break;
			
			case "db_pass":
			var dbPass = $(this).val();
			$(this).parent("dd").find(".balErr").remove();
			if((!dbPass.match(/^[!"#$%&\'()=~|\-^@\[;:\],.\/`{+*}<>?]+$/) && !dbPass.match(/^[0-9a-zA-Z]+$/) && dbPass != "")){
				$(this).parent("dd").append("<span class='balErr'>" + "パスワードに使用できる文字は半角英数字[0-9][a-z][A-Z]と一部の半角記号[! &quot; # - $ % &amp; &#039; ( ) = ~ | ^ @ [ ; : ] , . / ` { + * } &lt; &gt; ?]のみです。" + "</span>");
			}else if(dbPass == ""){
				$(this).parent("dd").append("<span class='balErr'>" + "接続ユーザーのパスワードを入力してください。" + "</span>");
			}
			break;
		}
	});

});
