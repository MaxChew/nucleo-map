<modal :size="'lg'" :show="popupStudentModal" :title="'Student Details'" @close="closeStudentPopup = false">
    <div class="max-w-lg bg-white rounded-xl">
        <div class="p-4">
            <div class="mb-4">
                <h3 class="text-lg font-bold text-gray-800">Profile Information</h3>
                <p><strong>Name:</strong> @{{ selectedStudent.name }}</p>
                <p><strong>IC:</strong> @{{ selectedStudent.ic }}</p>
                <p><strong>Phone:</strong> @{{ selectedStudent.phone }}</p>
                <p><strong>Email:</strong> @{{ selectedStudent.email }}</p>
                <p><strong>Location:</strong> @{{ selectedStudent.location }}</p>
            </div>

            <div>
                <h3 class="text-lg font-bold text-gray-800">Class History</h3>
                <table class="table-auto w-full border-collapse border border-gray-700 text-sm">
                    <thead>
                        <tr class="bg-gray-800 text-white">
                            <th class="border px-4 py-2 text-left">Date/Time</th>
                            <th class="border px-4 py-2 text-left">Class Status</th>
                            <th class="border px-4 py-2 text-left">Report Card Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="classData in selectedStudent.classes" :key="classData.id">
                            <td class="border px-4 py-2">@{{ classData.date_time }}</td>
                            <td class="border px-4 py-2" :class="classData.class_status === 'Active' ? 'text-success-500' : 'text-danger-500'">
                                @{{ classData.class_status }}
                            </td>
                            <td class="border px-4 py-2">@{{ classData.report_card_status }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</modal>
