/* Studio Tabi CMS — admin helpers: repeaters + media pickers */
(function () {
	'use strict';

	function nextIndex(rowsEl) {
		var rows = rowsEl.querySelectorAll('.stcms-row');
		var max = -1;
		rows.forEach(function (r) {
			var input = r.querySelector('input[name]');
			if (!input) return;
			var m = input.name.match(/\[(\d+)\]/);
			if (m) max = Math.max(max, parseInt(m[1], 10));
		});
		return max + 1;
	}

	function buildRow(base, index, cols) {
		var row = document.createElement('div');
		row.className = 'stcms-row';
		row.style.cssText = 'display:flex;gap:6px;margin-bottom:6px;align-items:center';
		Object.keys(cols).forEach(function (key) {
			var input = document.createElement('input');
			input.type = 'text';
			input.placeholder = cols[key];
			input.name = base + '[' + index + '][' + key + ']';
			input.style.flex = '1';
			row.appendChild(input);
		});
		var rm = document.createElement('button');
		rm.type = 'button';
		rm.className = 'button-link stcms-remove';
		rm.style.color = '#b32d2e';
		rm.textContent = '×';
		row.appendChild(rm);
		return row;
	}

	document.addEventListener('click', function (e) {
		// Add row
		if (e.target.classList.contains('stcms-add')) {
			e.preventDefault();
			var rep = e.target.closest('.stcms-repeater');
			var rowsEl = rep.querySelector('.stcms-rows');
			var cols = JSON.parse(rep.querySelector('.stcms-cols').textContent);
			rowsEl.appendChild(buildRow(rep.dataset.base, nextIndex(rowsEl), cols));
		}
		// Remove row
		if (e.target.classList.contains('stcms-remove')) {
			e.preventDefault();
			var row = e.target.closest('.stcms-row');
			var container = row.parentNode;
			row.remove();
			if (!container.querySelector('.stcms-row')) {
				// keep at least one empty row so the section stays usable
				var rep2 = container.closest('.stcms-repeater');
				var cols2 = JSON.parse(rep2.querySelector('.stcms-cols').textContent);
				container.appendChild(buildRow(rep2.dataset.base, 0, cols2));
			}
		}
		// Media picker (used on the options page)
		if (e.target.classList.contains('stcms-media-pick')) {
			e.preventDefault();
			var wrap = e.target.closest('.stcms-media');
			var frame = wp.media({ title: 'Selecionar imagem', multiple: false });
			frame.on('select', function () {
				var att = frame.state().get('selection').first().toJSON();
				wrap.querySelector('.stcms-media-id').value = att.id;
				var img = wrap.querySelector('.stcms-media-preview');
				img.src = att.sizes && att.sizes.thumbnail ? att.sizes.thumbnail.url : att.url;
				img.style.display = 'block';
				wrap.querySelector('.stcms-media-clear').style.display = 'inline-block';
			});
			frame.open();
		}
		if (e.target.classList.contains('stcms-media-clear')) {
			e.preventDefault();
			var wrap2 = e.target.closest('.stcms-media');
			wrap2.querySelector('.stcms-media-id').value = '';
			wrap2.querySelector('.stcms-media-preview').style.display = 'none';
			e.target.style.display = 'none';
		}
	});
})();
