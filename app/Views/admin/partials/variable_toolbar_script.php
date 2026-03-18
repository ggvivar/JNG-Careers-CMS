<script>
  window.TemplateEditors = window.TemplateEditors || {};

  function insertAtCursor(el, text) {
    if (!el) return;

    el.focus();

    if (typeof el.selectionStart === 'number' && typeof el.selectionEnd === 'number') {
      const start = el.selectionStart;
      const end = el.selectionEnd;
      const value = el.value || '';
      el.value = value.substring(0, start) + text + value.substring(end);
      el.selectionStart = el.selectionEnd = start + text.length;
    } else {
      el.value = (el.value || '') + text;
    }

    el.dispatchEvent(new Event('input', { bubbles: true }));
    el.dispatchEvent(new Event('change', { bubbles: true }));
  }

  document.addEventListener('click', function (e) {
    const btn = e.target.closest('.insert-variable-btn');
    if (!btn) return;

    const variable = btn.getAttribute('data-variable') || '';
    const targetId = btn.getAttribute('data-target') || '';
    const useEditor = btn.getAttribute('data-editor') === '1';

    if (!targetId || !variable) return;

    if (useEditor && window.TemplateEditors[targetId]) {
      const editor = window.TemplateEditors[targetId];
      editor.model.change(writer => {
        const insertPosition = editor.model.document.selection.getFirstPosition();
        writer.insertText(variable, insertPosition);
      });
      editor.editing.view.focus();
      return;
    }

    const target = document.getElementById(targetId);
    insertAtCursor(target, variable);
  });
</script>