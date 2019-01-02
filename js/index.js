
/* used for validation on client side, when change password on user profile */
function resetPassword() {
	var password = document.forms["resetPassword-form"]["password"].value;
	var confirmed_Password = document.forms["resetPassword-form"]["confirm_password"].value;

	if(password != confirmed_Password)
	{
		alert("Passwords don't match!");
	}
}

/* set minimum date selectable for endDate on appointment form */
function appointmentStartDateChanged() {
	$("#appointmentEndDate").removeAttr("disabled");
	$("#appointmentEndDate").val("");
	$("#appointmentEndDate").attr("min", $("#appointmentStartDate").val());
	
}
