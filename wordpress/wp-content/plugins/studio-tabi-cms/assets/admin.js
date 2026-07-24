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

		// Gallery: adicionar várias imagens
		if (e.target.classList.contains('stcms-gallery-add')) {
			e.preventDefault();
			var gwrap = e.target.closest('.stcms-gallery');
			var idsInput = gwrap.querySelector('.stcms-gallery-ids');
			var preview = gwrap.querySelector('.stcms-gallery-preview');
			var gframe = wp.media({ title: 'Selecionar imagens da galeria', multiple: 'add' });
			gframe.on('select', function () {
				var current = idsInput.value ? idsInput.value.split(',').filter(Boolean) : [];
				gframe.state().get('selection').forEach(function (att) {
					var a = att.toJSON();
					if (current.indexOf(String(a.id)) !== -1) return;
					current.push(String(a.id));
					var thumb = a.sizes && a.sizes.thumbnail ? a.sizes.thumbnail.url : a.url;
					var item = document.createElement('div');
					item.className = 'stcms-gallery-item';
					item.setAttribute('data-id', a.id);
					item.style.cssText = 'position:relative;width:84px;height:84px';
					item.innerHTML =
						'<img src="' + thumb + '" style="width:100%;height:100%;object-fit:cover;border:1px solid #dcdcde;border-radius:4px" />' +
						'<button type="button" class="stcms-gallery-remove" title="Remover" style="position:absolute;top:-7px;right:-7px;background:#b32d2e;color:#fff;border:none;border-radius:50%;width:20px;height:20px;cursor:pointer;line-height:18px;padding:0">×</button>';
					preview.appendChild(item);
				});
				idsInput.value = current.join(',');
			});
			gframe.open();
		}

		// Gallery: remover uma imagem
		if (e.target.classList.contains('stcms-gallery-remove')) {
			e.preventDefault();
			var item2 = e.target.closest('.stcms-gallery-item');
			var gwrap2 = e.target.closest('.stcms-gallery');
			var idsInput2 = gwrap2.querySelector('.stcms-gallery-ids');
			var rid = String(item2.getAttribute('data-id'));
			var list = idsInput2.value ? idsInput2.value.split(',').filter(Boolean) : [];
			idsInput2.value = list.filter(function (x) { return x !== rid; }).join(',');
			item2.remove();
		}
	});
})();
