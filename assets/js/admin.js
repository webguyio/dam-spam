(function() {
	'use strict';

	var damSpamAjaxWho = '';

	function damSpamAjaxProcess(sip, contx, sfunc, url, email) {
		email = email || '';
		damSpamAjaxWho = contx;
		var func_nonce = damSpamAjax.func_nonces[sfunc] || '';
		var data = {
			action: 'dam_spam_sfs_process',
			ip: sip,
			email: email,
			cont: contx,
			func: sfunc,
			ajax_url: url,
			nonce: damSpamAjax.nonce,
			func_nonce: func_nonce
		};
		var params = new URLSearchParams(data).toString();
		fetch(ajaxurl, {
			method: 'POST',
			headers: {
				'Content-Type': 'application/x-www-form-urlencoded',
			},
			body: params
		})
			.then(function(response) { return response.text(); })
			.then(damSpamAjaxReturnProcess)
			.catch(function(error) { console.error('AJAX Error:', error); });
	}

	function damSpamAjaxReturnProcess(response) {
		if (response === 'OK') {
			return false;
		}
		if (response.substring(0, 3) === 'err') {
			alert(response);
			return false;
		}
		if (response.substring(0, 4) === '\r\n\r\n') {
			alert(response);
			return false;
		}
		if (damSpamAjaxWho !== '') {
			var el = document.getElementById(damSpamAjaxWho);
			if (el) {
				el.innerHTML = response;
			}
		}
		return false;
	}

	function damSpamAjaxReportSpam(t, id, blog, url, email, ip, user) {
		damSpamAjaxWho = t;
		var data = {
			action: 'dam_spam_sfs_sub',
			blog_id: blog,
			comment_id: id,
			ajax_url: url,
			email: email,
			ip: ip,
			user: user,
			nonce: damSpamAjax.nonce
		};
		var params = new URLSearchParams(data).toString();
		fetch(ajaxurl, {
			method: 'POST',
			headers: {
				'Content-Type': 'application/x-www-form-urlencoded',
			},
			body: params
		})
			.then(function(response) { return response.text(); })
			.then(damSpamAjaxReturnSpam)
			.catch(function(error) { console.error('AJAX Error:', error); });
	}

	function damSpamAjaxReturnSpam(response) {
		if (!damSpamAjaxWho) return;
		damSpamAjaxWho.innerHTML = ' Spam Reported';
		damSpamAjaxWho.style.color = 'green';
		damSpamAjaxWho.style.fontWeight = 'bolder';
		if (response.indexOf('data submitted successfully') >= 0) {
			return false;
		}
		if (response.indexOf('recent duplicate entry') >= 0) {
			damSpamAjaxWho.innerHTML = ' Spam Already Reported';
			damSpamAjaxWho.style.color = 'yellow';
			damSpamAjaxWho.style.fontWeight = 'bolder';
			return false;
		}
		damSpamAjaxWho.textContent = ' Status: ' + response.replace(/<[^>]*>/g, '').trim();
		damSpamAjaxWho.style.color = 'red';
		damSpamAjaxWho.style.fontWeight = 'bolder';
		return false;
	}

	function damSpamToggle(checkboxId, targetId) {
		var checkbox = document.getElementById(checkboxId);
		var target = document.getElementById(targetId);
		if (checkbox && target) {
			target.style.display = checkbox.checked ? 'block' : 'none';
			checkbox.addEventListener('change', function() {
				target.style.display = this.checked ? 'block' : 'none';
			});
		}
	}

	function damSpamCheckFormStatus() {
		var checkForm = document.getElementById('check_form');
		var checkCreditCard = document.getElementById('check_credit_card');
		var checkWooForm = document.getElementById('check_woo_form');
		var checkGravityForm = document.getElementById('check_gravity_form');
		var checkWpForm = document.getElementById('check_wp_form');
		if (checkForm && checkCreditCard && checkWooForm && checkGravityForm && checkWpForm) {
			var disabled = checkForm.checked;
			checkCreditCard.disabled = disabled;
			checkWooForm.disabled = disabled;
			checkGravityForm.disabled = disabled;
			checkWpForm.disabled = disabled;
		}
	}

	function damSpamSortTable(n) {
		var table = document.getElementById('dam-spam-table');
		if (!table) return;
		var rows, switching, i, x, y, shouldSwitch, dir, switchcount = 0;
		switching = true;
		dir = 'asc';
		while (switching) {
			switching = false;
			rows = table.rows;
			for (i = 1; i < (rows.length - 1); i++) {
				shouldSwitch = false;
				x = rows[i].getElementsByTagName('TD')[n];
				y = rows[i + 1].getElementsByTagName('TD')[n];
				if (dir === 'asc') {
					if (x.innerHTML.toLowerCase() > y.innerHTML.toLowerCase()) {
						shouldSwitch = true;
						break;
					}
				} else if (dir === 'desc') {
					if (x.innerHTML.toLowerCase() < y.innerHTML.toLowerCase()) {
						shouldSwitch = true;
						break;
					}
				}
			}
			if (shouldSwitch) {
				rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
				switching = true;
				switchcount++;
			} else {
				if (switchcount === 0 && dir === 'asc') {
					dir = 'desc';
					switching = true;
				}
			}
		}
	}

	function damSpamSearch() {
		var input = document.getElementById('dam-spam-input');
		var table = document.getElementById('dam-spam-table');
		if (!input || !table) return;
		var filter = input.value.toUpperCase();
		var tr = table.getElementsByTagName('tr');
		for (var i = 0; i < tr.length; i++) {
			var td = tr[i].getElementsByTagName('td')[0];
			if (td) {
				var txtValue = td.textContent || td.innerText;
				if (txtValue.toUpperCase().indexOf(filter) > -1) {
					tr[i].style.display = '';
				} else {
					tr[i].style.display = 'none';
				}
			}
		}
	}

	function damSpamMarkAll(elements) {
		if (elements.length > 0) {
			for (var i = 0; i < elements.length; i++) {
				elements[i].checked = true;
			}
		} else {
			elements.checked = true;
		}
	}

	function damSpamUnmarkAll(elements) {
		if (elements.length > 0) {
			for (var i = 0; i < elements.length; i++) {
				elements[i].checked = false;
			}
		} else {
			elements.checked = false;
		}
	}

	function damSpamSortUserList(sortField) {
		var sortOrder = document.getElementById('sort_order');
		var form = document.getElementById('inactive-user-deleter-form');
		if (sortOrder && form) {
			sortOrder.value = sortField;
			form.submit();
		}
	}

	document.addEventListener('DOMContentLoaded', function() {
		damSpamToggle('redir', 'dam_spam_show_option');
		damSpamToggle('notify', 'dam_spam_show_notify');
		damSpamToggle('check_session', 'dam_spam_show_quick');
		damSpamToggle('check_multi', 'dam_spam_show_check_multi');
		var checkForm = document.getElementById('check_form');
		if (checkForm) {
			damSpamCheckFormStatus();
			checkForm.addEventListener('change', function() {
				if (this.dataset.status === 'valid') {
					damSpamCheckFormStatus();
				}
			});
		}
		var activationCheckbox = document.getElementById('dam_spam_require_activation');
		var autoDeleteCheckbox = document.getElementById('dam_spam_activation_auto_delete');
		if (activationCheckbox && autoDeleteCheckbox) {
			activationCheckbox.addEventListener('change', function() {
				if (this.checked) {
					autoDeleteCheckbox.disabled = false;
				} else {
					autoDeleteCheckbox.disabled = true;
					autoDeleteCheckbox.checked = false;
				}
			});
		}
		var lockedLinks = document.querySelectorAll('.row-actions .dam_spam_unlock a');
		lockedLinks.forEach(function(link) {
			var row = link.closest('tr');
			if (row) {
				row.classList.add('dam-spam-locked');
			}
		});
	});

	window.damSpamAjaxProcess = damSpamAjaxProcess;
	window.damSpamAjaxReportSpam = damSpamAjaxReportSpam;
	window.damSpamSortTable = damSpamSortTable;
	window.damSpamSearch = damSpamSearch;
	window.damSpamMarkAll = damSpamMarkAll;
	window.damSpamUnmarkAll = damSpamUnmarkAll;
	window.damSpamSortUserList = damSpamSortUserList;

})();