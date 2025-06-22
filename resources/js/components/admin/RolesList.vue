<template>
    <div class="roles-list">
        <h2 class="text-2xl font-bold mb-4">角色管理</h2>
        <div class="bg-white rounded-lg shadow">
            <div class="p-6">
                <p>角色列表内容将在这里显示</p>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'RolesList',
    data() {
        return {
            roles: []
        }
    }
}
</script> 