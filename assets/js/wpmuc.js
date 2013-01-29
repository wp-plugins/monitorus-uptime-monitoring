var wpmuc = {
    signup_link: document.getElementById("signup_link"),
    formButtons: document.getElementById("formButtons"),
    signin_link: document.getElementById("signin_link"),
    emailTd: document.getElementById('emailTd'),
	
	
	settings:
    {
        show_pass_field: function (){
            jQuery('#pass_link').remove();
            jQuery('#pass_field').html('<input style="margin-left: 5px; margin-right: 1px; width: 250px; margin-top: 1px; margin-bottom: 1px;" type="password" name="settings[pass]" value="">');
        },
		
		act: function(url){
			jQuery('.wpmuc.message').html('<p>Please wait</p>');
			document.location.href = url;
		},
		
		signup: function(){
			document.getElementById('isSignUp').value = true;
			var rows = document.signin_form.getElementsByTagName("tr");
				for(var i = 0; i < rows.length; i++){
					if(rows[i].className == "hidden") {
					   rows[i].parentNode.removeChild(rows[i]);
					   i--;
					}
				}
		},
		
		showSigninForm: function(){
			var passwordRow = document.getElementById('passTr');
			var rows = document.signin_form.getElementsByTagName("tr");
			if(passwordRow){
				passwordRow.removeAttribute("class");
			}
			signup_link.removeAttribute("class");
			formButtons.innerHTML = '<input id="signin_button" type="submit" value="Sign in" class="button-primary" />';
			emailTd.innerHTML = '<input style="margin-left: 5px; margin-right: 1px; width: 250px; margin-top: 1px; margin-bottom: 1px;" type="text" name="settings[email]" value="">';
			signin_link.setAttribute("class","hidden");
			
			rows[2].setAttribute("class","hidden"); //name
			rows[3].setAttribute("class","hidden"); //surname
			this.show_pass_field();
		}, 
		
		showSignupForm: function(){
			var rows = document.signin_form.getElementsByTagName("tr");
			emailTd.innerHTML = '<input style="margin-left: 5px; margin-right: 1px; width: 250px; margin-top: 1px; margin-bottom: 1px;" type="text" name="settings[email]" value="">';
			for(var i = 2; i < rows.length; i++){
				rows[i].removeAttribute("class");
			}
			signup_link.setAttribute("class","hidden");
			signin_link.removeAttribute("class");
			formButtons.innerHTML = '<input type="submit" id = "signup_button" onclick="wpmuc.settings.signup()" class="button-primary" value="Submit"/>';
		},
		
		validate_form: function(){
			var inputs = document.signin_form.getElementsByTagName('input');
			var isValid = this.validate(inputs[0].value, inputs[1].value); //params: email, password
			if(isValid != 'true'){
				var messageBox = document.getElementById('messageBox');
				messageBox.innerHTML = '<p>'+isValid+'</p>';
				messageBox.className = "wpmuc message";
				return false;
			}
		},
		
		preventSubmit: function(e){
			e = e || event;
			return (e.keyCode || e.which || e.charCode || 0) !== 13;
		},
		
		validate: function(email, pass){ 
			if(email == '') return 'Email is required.';
			else {
				var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
				if(!re.test(email)) return 'Please enter valid e-mail.';
			}
			if(pass.trim() == '' || pass.length < 6) return 'Please enter password (at least 6 characters).';
			else return 'true';
		},
		
		changeChartSettings: function(checkbox, rowNumber){
			if(checkbox.checked){
				document.getElementById("select_"+rowNumber).setAttribute("class","");
			} else {
				document.getElementById("select_"+rowNumber).setAttribute("class","hidden");
			}
		}
    }
}