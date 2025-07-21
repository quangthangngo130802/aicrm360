const columns = [
    {
        data: "DT_RowIndex",
        name: "DT_RowIndex",
        title: "SST",
        orderable: false,
        searchable: false,
        width: "4%",
    },
    {
        data: "code",
        name: "code",
        title: "Mã",
    },
    {
        data: "name",
        name: "name",
        title: "Họ tên",
    },

    {
        data: "phone",
        name: "phone",
        title: "Số điện thoại",
        render(data, type, row) {
            return data || '<small class="text-muted">Chưa cập nhật...</small>';
        },
    },
    {
        data: "email",
        name: "email",
        title: "Email",
        orderable: false,
    },
    {
        data: "address",
        name: "address",
        title: "Địa chỉ",
        render: (data) =>
            data || '<small class="text-muted">Chưa cập nhật...</small>',
    },

    {
        data: "gender",
        name: "gender",
        title: "Giới tính",
        orderable: false,
        searchable: false,
        render: function (data) {
            const genderMap = {
                male: { label: "Nam", class: "primary" },
                female: { label: "Nữ", class: "danger" },
                other: { label: "Khác", class: "secondary" },
            };

            const gender = genderMap[data];
            return gender
                ? `<span class="badge bg-${gender.class}">${gender.label}</span>`
                : "";
        },
    },
    {
        data: "username",
        name: "username",
        title: "Người thêm",
        orderable: false,
    },
];
