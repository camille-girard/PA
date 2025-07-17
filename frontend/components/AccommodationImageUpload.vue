<template>
  <div class="accommodation-image-upload">
    <!-- Images existantes -->
    <div v-if="images.length > 0" class="existing-images">
      <h3 class="text-lg font-semibold mb-4">Images actuelles</h3>
      <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
        <div
          v-for="image in images"
          :key="image.id"
          class="relative group"
        >
          <img
            :src="image.url"
            :alt="image.alt || 'Image du logement'"
            class="w-full h-32 object-cover rounded-lg"
          />
          
          <!-- Badge image principale -->
          <div
            v-if="image.isMain"
            class="absolute top-2 left-2 bg-blue-500 text-white px-2 py-1 rounded text-xs"
          >
            Principale
          </div>
          
          <!-- Actions -->
          <div class="absolute inset-0 bg-black bg-opacity-50 opacity-0 group-hover:opacity-100 transition-opacity duration-200 rounded-lg flex items-center justify-center">
            <div class="flex gap-2">
              <button
                v-if="!image.isMain"
                :disabled="loading"
                class="bg-blue-500 text-white px-2 py-1 rounded text-xs hover:bg-blue-600"
                @click="setMainImage(image.id)"
              >
                Définir comme principale
              </button>
              <button
                :disabled="loading"
                class="bg-red-500 text-white px-2 py-1 rounded text-xs hover:bg-red-600"
                @click="deleteImage(image.id)"
              >
                Supprimer
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Zone d'upload -->
    <div class="upload-zone mt-6">
      <h3 class="text-lg font-semibold mb-4">Ajouter des images</h3>
      
      <UFileInput
        ref="fileInput"
        multiple
        accept="image/*"
        :disabled="loading"
        class="mb-4"
        @upload="handleImageUpload"
      />
      
      <!-- Prévisualisation des nouvelles images -->
      <div v-if="newImages.length > 0" class="new-images-preview">
        <h4 class="text-md font-medium mb-2">Nouvelles images à uploader</h4>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
          <div
            v-for="(image, index) in newImages"
            :key="index"
            class="relative"
          >
            <img
              :src="image.preview"
              :alt="`Nouvelle image ${index + 1}`"
              class="w-full h-32 object-cover rounded-lg"
            />
            <button
              :disabled="loading"
              class="absolute top-1 right-1 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs hover:bg-red-600"
              @click="removeNewImage(index)"
            >
              ×
            </button>
          </div>
        </div>
      </div>

      <div v-if="newImages.length > 0" class="mt-4 flex gap-2">
        <button
          :disabled="loading"
          class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 disabled:opacity-50"
          @click="uploadNewImages"
        >
          {{ loading ? 'Upload en cours...' : 'Uploader les images' }}
        </button>
        <button
          :disabled="loading"
          class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600"
          @click="clearNewImages"
        >
          Annuler
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
interface AccommodationImage {
  id: number | string
  url: string
  alt?: string
  isMain?: boolean
}

interface NewImage {
  file: File
  preview: string
}

interface Props {
  accommodationId?: number | undefined
  images: AccommodationImage[]
  maxImages?: number
}

const props = withDefaults(defineProps<Props>(), {
  accommodationId: undefined,
  maxImages: 10
})

const emit = defineEmits<{
  'images-updated': [images: AccommodationImage[]]
  'add-images': [files: File[]]
  'remove-image': [id: string | number]
}>()

const { $toast } = useNuxtApp()
const fileInput = ref()
const loading = ref(false)
const newImages = ref<NewImage[]>([])

const handleImageUpload = (files: File[]) => {
  const remainingSlots = props.maxImages - props.images.length - newImages.value.length
  const filesToAdd = files.slice(0, remainingSlots)
  
  for (const file of filesToAdd) {
    if (file.type.startsWith('image/')) {
      const preview = URL.createObjectURL(file)
      newImages.value.push({
        file,
        preview
      })
    }
  }
  
  // Si on est en mode création/édition sans accommodationId, émettre directement
  if (!props.accommodationId) {
    emit('add-images', filesToAdd)
  }
}

const removeNewImage = (index: number) => {
  const image = newImages.value[index]
  URL.revokeObjectURL(image.preview)
  newImages.value.splice(index, 1)
}

const clearNewImages = () => {
  newImages.value.forEach(image => URL.revokeObjectURL(image.preview))
  newImages.value = []
}

const uploadNewImages = async () => {
  if (newImages.value.length === 0) return

  // Si pas d'accommodationId, on est en mode création
  if (!props.accommodationId) {
    const files = newImages.value.map(img => img.file)
    emit('add-images', files)
    clearNewImages()
    return
  }

  loading.value = true
  
  try {
    const formData = new FormData()
    newImages.value.forEach((image, index) => {
      formData.append(`image_${index}`, image.file)
    })

    const response = await $fetch(`/api/accommodations/${props.accommodationId}/images`, {
      method: 'POST',
      body: formData
    })

    // Mettre à jour les images existantes
    const updatedImages = [...props.images]
    if (response.images) {
      response.images.forEach((newImage: { url: string; isMain: boolean }) => {
        updatedImages.push({
          id: Date.now() + Math.random(), // Temporary ID
          url: newImage.url,
          isMain: newImage.isMain
        })
      })
    }

    emit('images-updated', updatedImages)
    clearNewImages()
    $toast.success('Images uploadées avec succès')
  } catch (error: unknown) {
    console.error('Erreur lors de l\'upload:', error)
    $toast.error((error as { data?: { message?: string } })?.data?.message || 'Erreur lors de l\'upload des images')
  } finally {
    loading.value = false
  }
}

const setMainImage = async (imageId: number) => {
  loading.value = true
  
  try {
    await $fetch(`/api/accommodations/${props.accommodationId}/images/${imageId}/main`, {
      method: 'PUT'
    })

    // Mettre à jour les images localement
    const updatedImages = props.images.map(image => ({
      ...image,
      isMain: image.id === imageId
    }))

    emit('images-updated', updatedImages)
    $toast.success('Image principale mise à jour')
  } catch (error: unknown) {
    console.error('Erreur lors de la mise à jour:', error)
    $toast.error((error as { data?: { message?: string } })?.data?.message || 'Erreur lors de la mise à jour de l\'image principale')
  } finally {
    loading.value = false
  }
}

const deleteImage = async (imageId: number | string) => {
  if (!confirm('Êtes-vous sûr de vouloir supprimer cette image ?')) {
    return
  }

  // Si pas d'accommodationId, on est en mode création
  if (!props.accommodationId) {
    emit('remove-image', imageId)
    return
  }

  loading.value = true
  
  try {
    await $fetch(`/api/accommodations/${props.accommodationId}/images/${imageId}`, {
      method: 'DELETE'
    })

    // Supprimer l'image de la liste locale
    const updatedImages = props.images.filter(image => image.id !== imageId)
    emit('images-updated', updatedImages)
    $toast.success('Image supprimée avec succès')
  } catch (error: unknown) {
    console.error('Erreur lors de la suppression:', error)
    $toast.error((error as { data?: { message?: string } })?.data?.message || 'Erreur lors de la suppression de l\'image')
  } finally {
    loading.value = false
  }
}

// Nettoyer les URLs d'objet lors du démontage
onUnmounted(() => {
  clearNewImages()
})
</script>

<style scoped>
.accommodation-image-upload {
  @apply p-4 border border-gray-200 rounded-lg;
}

.existing-images {
  @apply mb-6;
}

.upload-zone {
  @apply border-t border-gray-200 pt-6;
}

.new-images-preview {
  @apply mt-4 p-4 bg-gray-50 rounded-lg;
}
</style>