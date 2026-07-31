(function () {
	'use strict';

	function mediaReady() {
		if (typeof wp === 'undefined' || !wp.media) {
			window.alert('A biblioteca de mídia do WordPress não carregou nesta tela. Recarregue a página (F5). Se continuar, pode ser conflito com outro plugin.');
			return false;
		}
		return true;
	}

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

	function buildRow(base, index, cols, rep) {
		var row = document.createElement('div');
		row.className = 'stcms-row';
		row.style.cssText = 'display:flex;gap:6px;margin-bottom:6px;align-items:center;flex-wrap:wrap';
		var modelo = rep ? rep.querySelector('.stcms-destino') : null;
		Object.keys(cols).forEach(function (key) {
			var input = document.createElement('input');
			input.type = 'text';
			input.className = 'stcms-campo-' + key;
			input.placeholder = cols[key];
			input.name = base + '[' + index + '][' + key + ']';
			input.style.flex = '1';
			row.appendChild(input);
			if (modelo && key === 'url') {
				var copia = modelo.cloneNode(true);
				var sel = copia.querySelector('.stcms-destino-select');
				if (sel) { sel.value = ''; }
				var ed = copia.querySelector('.stcms-destino-editar');
				if (ed) { ed.style.display = 'none'; ed.removeAttribute('href'); }
				row.appendChild(copia);
			}
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
		var head = e.target.closest && e.target.closest('.stcms-card-head');
		if (head) {
			var card = head.closest('.stcms-card');
			var collapsed = card.classList.toggle('is-collapsed');
			head.setAttribute('aria-expanded', collapsed ? 'false' : 'true');
			return;
		}

		if (e.target.classList.contains('stcms-add')) {
			e.preventDefault();
			var rep = e.target.closest('.stcms-repeater');
			var rowsEl = rep.querySelector('.stcms-rows');
			var cols = JSON.parse(rep.querySelector('.stcms-cols').textContent);
			rowsEl.appendChild(buildRow(rep.dataset.base, nextIndex(rowsEl), cols, rep));
		}
		if (e.target.classList.contains('stcms-remove')) {
			e.preventDefault();
			var row = e.target.closest('.stcms-row');
			var container = row.parentNode;
			row.remove();
			if (!container.querySelector('.stcms-row')) {
				var rep2 = container.closest('.stcms-repeater');
				var cols2 = JSON.parse(rep2.querySelector('.stcms-cols').textContent);
				container.appendChild(buildRow(rep2.dataset.base, 0, cols2, rep2));
			}
		}
		if (e.target.classList.contains('stcms-media-pick')) {
			e.preventDefault();
			if (!mediaReady()) return;
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

		if (e.target.classList.contains('stcms-gallery-add')) {
			e.preventDefault();
			if (!mediaReady()) return;
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

		if (e.target.classList.contains('stcms-docs-add')) {
			e.preventDefault();
			if (!mediaReady()) return;
			var dwrap = e.target.closest('.stcms-docs');
			var dInput = dwrap.querySelector('.stcms-docs-ids');
			var dList = dwrap.querySelector('.stcms-docs-list');
			var dframe = wp.media({
				title: 'Selecionar PDFs',
				multiple: 'add',
				library: { type: 'application/pdf' }
			});
			dframe.on('select', function () {
				var current = dInput.value ? dInput.value.split(',').filter(Boolean) : [];
				dframe.state().get('selection').forEach(function (att) {
					var a = att.toJSON();
					if (current.indexOf(String(a.id)) !== -1) return;
					current.push(String(a.id));
					var name = a.title || a.filename || 'documento.pdf';
					var item = document.createElement('div');
					item.className = 'stcms-doc-item';
					item.setAttribute('data-id', a.id);
					item.style.cssText = 'display:flex;align-items:center;gap:8px;background:#f7f8fa;border:1px solid #e6e8ec;border-radius:8px;padding:6px 10px';
					item.innerHTML =
						'<span class="dashicons dashicons-media-document" style="color:#b32d2e"></span>' +
						'<span style="flex:1;font-size:13px"></span>' +
						'<button type="button" class="stcms-doc-remove" title="Remover" style="background:#fff;border:1px solid #e6e8ec;color:#b32d2e;border-radius:50%;width:22px;height:22px;cursor:pointer;line-height:1">×</button>';
					item.querySelector('span[style*="flex"]').textContent = name;
					dList.appendChild(item);
				});
				dInput.value = current.join(',');
			});
			dframe.open();
		}

		if (e.target.classList.contains('stcms-doc-remove')) {
			e.preventDefault();
			var ditem = e.target.closest('.stcms-doc-item');
			var dwrap2 = e.target.closest('.stcms-docs');
			var dInput2 = dwrap2.querySelector('.stcms-docs-ids');
			var did = String(ditem.getAttribute('data-id'));
			var dlist = dInput2.value ? dInput2.value.split(',').filter(Boolean) : [];
			dInput2.value = dlist.filter(function (x) { return x !== did; }).join(',');
			ditem.remove();
		}
	});
})();

(function () {
	document.addEventListener('click', function (e) {
		var aba = e.target.closest ? e.target.closest('.stcms-abas .nav-tab') : null;
		if (!aba) { return; }
		e.preventDefault();
		var caixa = aba.closest('.stcms-idiomas');
		if (!caixa) { return; }
		var alvo = aba.getAttribute('data-aba');
		caixa.querySelectorAll('.stcms-abas .nav-tab').forEach(function (t) {
			t.classList.toggle('nav-tab-active', t === aba);
		});
		caixa.querySelectorAll('.stcms-painel').forEach(function (p) {
			p.style.display = p.getAttribute('data-painel') === alvo ? '' : 'none';
		});
		if (window.tinymce) {
			window.tinymce.editors.forEach(function (ed) {
				if (ed.getContainer() && caixa.contains(ed.getContainer())) {
					try { ed.execCommand('mceRepaint'); } catch (err) {}
				}
			});
			window.dispatchEvent(new Event('resize'));
		}
	});

	document.addEventListener('submit', function (e) {
		if (e.target && e.target.id === 'post' && window.tinymce) {
			try { window.tinymce.triggerSave(); } catch (err) {}
		}
	}, true);

	document.addEventListener('DOMContentLoaded', function () {
		document.querySelectorAll('.stcms-idiomas').forEach(function (caixa) {
			var painel = caixa.querySelector('.stcms-painel[data-painel="en"]');
			var marca = caixa.querySelector('.stcms-aba-status');
			if (!painel || !marca) { return; }
			var campos = painel.querySelectorAll('input[type="text"], textarea');
			var preenchido = Array.prototype.some.call(campos, function (c) {
				return c.value && c.value.trim() !== '';
			});
			marca.textContent = preenchido ? ' ●' : '';
			marca.style.color = '#1a7f37';
			marca.title = preenchido ? 'Este item já tem tradução' : '';
		});
	});
})();

(function () {
	function campoDoSeletor(sel) {
		var linha = sel.closest('.stcms-row');
		return linha ? linha.querySelector('.stcms-campo-url') : null;
	}

	function atualizarEditar(sel) {
		var link = sel.parentNode.querySelector('.stcms-destino-editar');
		if (!link) { return; }
		var opcao = sel.options[sel.selectedIndex];
		var url = opcao ? opcao.getAttribute('data-editar') : '';
		if (url) {
			link.href = url;
			link.style.display = '';
		} else {
			link.removeAttribute('href');
			link.style.display = 'none';
		}
	}

	document.addEventListener('change', function (e) {
		if (!e.target.classList || !e.target.classList.contains('stcms-destino-select')) { return; }
		var campo = campoDoSeletor(e.target);
		if (campo && e.target.value) {
			campo.value = e.target.value;
			campo.dispatchEvent(new Event('input', { bubbles: true }));
		}
		atualizarEditar(e.target);
	});

	document.addEventListener('input', function (e) {
		if (!e.target.classList || !e.target.classList.contains('stcms-campo-url')) { return; }
		var linha = e.target.closest('.stcms-row');
		var sel = linha ? linha.querySelector('.stcms-destino-select') : null;
		if (!sel) { return; }
		sel.value = Array.prototype.some.call(sel.options, function (o) { return o.value === e.target.value; })
			? e.target.value : '';
		atualizarEditar(sel);
	});

	document.addEventListener('DOMContentLoaded', function () {
		document.querySelectorAll('.stcms-destino-select').forEach(atualizarEditar);
	});
})();

(function () {
	document.addEventListener('click', function (e) {
		var link = e.target.closest ? e.target.closest('.stcms-indice-link') : null;
		if (!link) { return; }
		var alvo = document.getElementById('stcms-' + link.getAttribute('data-alvo'));
		if (!alvo) { return; }
		var cabeca = alvo.querySelector('.stcms-card-head');
		if (cabeca && cabeca.getAttribute('aria-expanded') === 'false') { cabeca.click(); }
	});
})();
