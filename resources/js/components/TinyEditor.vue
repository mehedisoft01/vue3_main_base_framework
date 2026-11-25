<script setup>
    import { ref, watch } from 'vue'
    import Editor from '@tinymce/tinymce-vue'

    // =====================
    // TinyMCE IMPORTS START
    // =====================

    import 'tinymce/tinymce'

    import 'tinymce/themes/silver'

    import 'tinymce/icons/default'

    import 'tinymce/models/dom'

    import 'tinymce/skins/ui/oxide/skin.min.css'
    // import 'tinymce/skins/content/default/content.min.css'

    import 'tinymce/plugins/table'
    import 'tinymce/plugins/link'
    import 'tinymce/plugins/lists'
    import 'tinymce/plugins/code'
    import 'tinymce/plugins/image'
    import 'tinymce/plugins/help'
    import 'tinymce/plugins/wordcount'

    // ===================
    // TinyMCE IMPORTS END
    // ===================

    const props = defineProps({
        modelValue: {
            type: String,
            default: ''
        }
    });

    const emit = defineEmits(['update:modelValue'])

    const content = ref(props.modelValue)

    watch(content, (value) => {
        emit('update:modelValue', value)
    });

    watch(
        () => props.modelValue,
        (value) => {
            content.value = value
        }
    )
</script>

<template>
    <Editor
            licenseKey="gpl"
            v-model="content"
            :init="{
             height: 300,
             menubar: false,
             statusbar: false,
             branding: false,



            plugins: [
                'lists link image table code help wordcount'
            ],

            toolbar:
                'undo redo | blocks | bold italic underline | \
                alignleft aligncenter alignright alignjustify | \
                bullist numlist outdent indent | \
                image table link | code',

        }"
    />
</template>