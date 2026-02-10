import { defineStore } from 'pinia'
import { ref } from 'vue'

export const useSearchStore = defineStore('search', () => {
    const wordsss = ref('')
    const gDocsOwnerId = ref('') 
    const gDocsEmail = ref('')
    const gDocsType = ref('')
    const gDocsLastOpened = ref('')
    const gDocsCreated = ref('')
    const gDocsUrl = ref('')
    const gDocsLink = ref('')
    const gDocsName = ref('')
    const gDocsId = ref('') 
    const deptId = ref('') 

    function setWordsss(value) {
        wordsss.value = value
    }

    function setOwnerId(value) {
        gDocsOwnerId.value = value
    }

    function setEmail(value) {
        gDocsEmail.value = value
    }

    function setType(value) {
        gDocsType.value = value
    }

    function setLastOpened(value) {
        gDocsLastOpened.value = value
    }

    function setCreated(value) {
        gDocsCreated.value = value
    }

    function setUrl(value) {
        gDocsUrl.value = value
    }

    function setLink(value) {
        gDocsLink.value = value
    }

    function setName(value) {
        gDocsName.value = value
    }

    function setId(value) {
        gDocsId.value = value
    }

    function setDeptId(value) {
        deptId.value = value
    }

    return { 
        wordsss,
        gDocsOwnerId,
        gDocsEmail,
        gDocsType,
        gDocsLastOpened,
        gDocsCreated,
        gDocsUrl,
        gDocsLink,
        gDocsName,
        gDocsId,
        deptId,

        setWordsss,
        setOwnerId,
        setEmail,
        setType,
        setLastOpened,
        setCreated,
        setUrl,
        setLink,
        setName,
        setId,
        setDeptId
    }
})
