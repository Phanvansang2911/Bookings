<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ITABookings - Tài xế</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root{--p:#003087;--d:#d32f2f;--s:#2e7d32;--l:#f8fbff}
        *{margin:0;padding:0;box-sizing:border-box}
        body{font-family:'Inter',sans-serif;background:#f0f4f8;color:#333}
        .container{max-width:1480px;margin:20px auto;background:#fff;border-radius:16px;overflow:hidden;box-shadow:0 12px 40px rgba(0,0,0,.12)}
        .header{background:var(--p);color:#fff;padding:20px 40px;display:flex;justify-content:space-between;align-items:center;position:relative}
        .header::after{content:'';position:absolute;bottom:0;left:0;right:0;height:6px;background:linear-gradient(90deg,#00c853,#ff9100,#ff1744,#0066ff)}
        .logo-area{display:flex;align-items:center;gap:20px}
        .logo-area img{height:65px;border-radius:12px;border:3px solid #fff}
        .logo-area div{font-size:28px;font-weight:700}
        .user-area{display:flex;align-items:center;gap:25px}
        .logout-btn{background:var(--d);color:#fff;border:none;padding:12px 28px;border-radius:50px;font-weight:600;cursor:pointer}
        .alert{background:#ffebee;color:#c62828;padding:20px;text-align:center;font-size:21px;font-weight:600}
        .section{padding:40px 50px}
        .title{font-size:25px;font-weight:700;color:var(--p);margin:30px 0 20px}
        .filter{display:flex;gap:15px;flex-wrap:wrap;align-items:center;margin-bottom:30px}
        .filter input,.filter select,.filter button{padding:13px 20px;border-radius:10px;border:1px solid #ddd;font-size:16px}
        .filter button{background:var(--p);color:#fff;border:none;cursor:pointer;font-weight:600}
        table{width:100%;border-collapse:collapse;margin-top:20px;font-size:15px}
        th{background:var(--p);color:#fff;padding:16px;text-align:left}
        td{padding:14px;border-bottom:1px solid #eee}
        tr:nth-child(even){background:var(--l)}
        .link{color:var(--p);font-weight:600;text-decoration:none}
        .link:hover{text-decoration:underline}
        .total{margin:50px 0;padding:35px;background:#e3f2fd;border-radius:16px;text-align:center;font-size:25px}
        .total strong{color:var(--p);font-size:40px}
        .chart-controls{display:flex;gap:15px;align-items:center;margin:30px 0;flex-wrap:wrap}
        .charts{display:grid;grid-template-columns:1fr 1fr;gap:40px;margin-top:20px}
        .chart-box{background:#fff;padding:30px;border-radius:18px;box-shadow:0 10px 30px rgba(0,0,0,.1);text-align:center;height:420px}
        .chart-title{font-size:22px;font-weight:700;color:var(--p);margin-bottom:15px}
        canvas{width:100%!important;height:340px!important}
        #chartYear{grid-column:1/-1;height:460px}
        #chartYear canvas{height:380px!important}
        .modal{display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.88);z-index:9999;justify-content:center;align-items:center;padding:20px}
        .modal.active{display:flex}
        .modal-content{background:#fff;max-width:960px;width:100%;border-radius:22px;overflow:hidden;max-height:95vh;overflow-y:auto}
        .modal-header{background:var(--p);color:#fff;padding:30px 45px;display:flex;justify-content:space-between;align-items:center}
        .modal-close{font-size:40px;cursor:pointer}
        .modal-body{padding:50px;font-size:18px}
        .modal-body h2{text-align:center;color:var(--p);border-bottom:5px solid var(--p);padding-bottom:15px;margin:50px 0 30px;font-size:28px}
        .modal-body td:first-child{font-weight:700;color:var(--p);width:40%}
        .price-box{background:#f8f9fa;padding:40px;border-radius:18px;text-align:center;margin:40px 0;border:3px dashed var(--p)}
        .earn{color:var(--s);font-size:32px;font-weight:700}
        .btn-red{background:var(--d);color:#fff;padding:22px 90px;font-size:25px;border:none;border-radius:50px;cursor:pointer;font-weight:700}
        @media(max-width:1024px){.charts{grid-template-columns:1fr}.chart-box{height:380px}canvas{height:300px!important}}
        @media(max-width:768px){
            .header{flex-direction:column;text-align:center;gap:15px}
            .filter{flex-direction:column}
            .filter>*{width:100%}
            .section{padding:30px 20px}
            table,thead,tbody,th,td,tr{display:block}
            thead tr{position:absolute;top:-9999px;left:-9999px}
            tr{border:1px solid #ccc;margin-bottom:15px;border-radius:12px;overflow:hidden;background:#fff}
            td{border:none;position:relative;padding-left:50%;text-align:right;padding:12px}
            td:before{content:attr(data-label);position:absolute;left:15px;width:45%;font-weight:700;text-align:left;color:var(--p)}
        }
        .chart-btn {
    padding: 14px 28px;
    border: none;
    border-radius: 50px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    background: #e3f2fd;
    color: #003087;
    min-width: 120px;
    box-shadow: 0 4px 15px rgba(0, 48, 135, 0.15);
}

.chart-btn:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(0, 48, 135, 0.25);
}

.chart-btn.active {
    background: #003087 !important;
    color: white !important;
    box-shadow: 0 6px 20px rgba(0, 48, 135, 0.4);
}

.chart-btn.refresh {
    background: linear-gradient(45deg, #00c853, #64dd17);
    color: white;
    font-weight: 700;
    padding: 14px 20px;
}

.chart-btn.refresh:hover {
    background: linear-gradient(45deg, #00b248, #4caf50);
    transform: translateY(-3px) scale(1.05);
}
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <div class="logo-area">
            <img src="https://i.imgur.com/8e2f0lP.png" alt="Logo">
            <div>ITABookings</div>
        </div>
        <div class="user-area">
            <div>Xin chào, <strong>Nguyễn Văn A</strong></div>
            <button class="logout-btn" onclick="if(confirm('Đăng xuất?')) alert('Đã đăng xuất!')">Đăng xuất</button>
        </div>
    </div>

    <div class="alert">
        Có <strong id="pending-count">0</strong> chuyến đang chờ tài xế nhận
    </div>

    <div class="section">
        <div class="title">Tìm kiếm chuyến chờ</div>
        <div class="filter">
            <input type="date" id="date-filter">
            <select id="pickup-filter"><option value="">Tất cả điểm đón</option></select>
            <select id="dropoff-filter"><option value="">Tất cả điểm đến</option></select>
            <button onclick="loadPending()">Tìm kiếm</button>
            <button onclick="document.getElementById('date-filter').value='';document.getElementById('pickup-filter').value='';document.getElementById('dropoff-filter').value='';loadPending()" style="background:#666">Xóa lọc</button>
        </div>

        <table>
            <thead><tr>
                <th>Khách</th><th>Đón</th><th>Trả</th><th>Xe</th><th>Số khách</th><th>Hành động</th><th>Trạng thái</th>
            </tr></thead>
            <tbody id="pending-body"><tr><td colspan="7" style="text-align:center;padding:60px;color:#999">Đang tải...</td></tr></tbody>
        </table>
    </div>

    <div class="section">
        <div class="title">Chuyến đã nhận</div>
        <table>
            <thead><tr>
                <th>Mã đơn</th><th>Đón</th><th>Trả</th><th>Xe</th><th>Khách</th><th>Ngày nhận</th><th>Thu</th>
            </tr></thead>
            <tbody id="accepted-body"></tbody>
        </table>

        <div class="total">
            Tổng: <strong id="total-trips">0</strong> chuyến | Thu nhập: <strong id="total-revenue">0 đ</strong>
        </div>

        <div class="title">
            Thống kê theo:
            <div class="chart-controls">
                <select id="chart-period">
                    <option value="month">Tháng này</option>
                    <option value="quarter">Quý này</option>
                    <option value="year">Năm nay</option>
                </select>
                <button onclick="loadCharts()">Cập nhật biểu đồ</button>
            </div>
        </div>

        <div class="charts">
            <div class="chart-box"><div class="chart-title">Theo tuần (tháng này)</div><canvas id="chartMonth"></canvas></div>
            <div class="chart-box"><div class="chart-title">Theo tháng (quý này)</div><canvas id="chartQuarter"></canvas></div>
            <div class="chart-box"><div class="chart-title">Theo tháng (năm nay)</div><canvas id="chartYear"></canvas></div>
        </div>
    </div>
</div>

<div class="modal" id="detailModal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Chi tiết đơn hàng</h2>
            <span class="modal-close" onclick="closeModal()">×</span>
        </div>
        <div class="modal-body">
            <h2>Thông tin khách</h2>
            <table><tr><td>Tên:</td><td id="m_name">-</td></tr>
            <tr><td>Hành trình:</td><td id="m_route">-</td></tr>
            <tr><td>Số khách:</td><td id="m_pass">-</td></tr></table>
            <h2>Yêu cầu xe</h2>
            <table><tr><td>Loại xe:</td><td id="m_type">-</td></tr></table>
            <h2>Thông tin đơn</h2>
            <div class="price-box">
                <div><strong>Mã đơn:</strong> <span style="font-size:32px;color:var(--p)" id="m_code">-</span></div>
                <div style="margin:30px 0"><strong>Bạn nhận:</strong> <span class="earn">1.500.000 đ</span></div>
            </div>
            <div style="text-align:center">
                <button class="btn-red" onclick="acceptTrip()">NHẬN ĐƠN NGAY</button>
            </div>
        </div>
    </div>
</div>

<script>
const provinces = ["Hà Nội","Huế","Quảng Ninh","Cao Bằng","Lạng Sơn","Lai Châu","Điện Biên","Sơn La","Thanh Hóa","Nghệ An","Hà Tĩnh","Tuyên Quang","Lào Cai","Thái Nguyên","Phú Thọ","Bắc Ninh","Hưng Yên","Hải Phòng","Ninh Bình","Quảng Trị","Đà Nẵng","Quảng Ngãi","Gia Lai","Khánh Hòa","Lâm Đồng","Đắk Lắk","Thành phố Hồ Chí Minh","Đồng Nai","Tây Ninh","Cần Thơ","Vĩnh Long","Đồng Tháp","Cà Mau","An Giang"];
provinces.forEach(p=>['pickup','dropoff'].forEach(x=>document.getElementById(x+'-filter').innerHTML+=`<option>${p}</option>`));

let currentId=0, charts={};
function closeModal(){document.getElementById('detailModal').classList.remove('active')}
document.getElementById('detailModal').onclick=e=>e.target.id==='detailModal'&&closeModal();
document.onkeydown=e=>e.key==='Escape'&&closeModal();

function showDetail(id){
    currentId=id;
    fetch(`api/trip_detail.php?id=${id}`).then(r=>r.json()).then(d=>{
        if(d.error)return alert("Không tìm thấy chuyến!");
        document.getElementById('m_name').textContent=d.customer_name;
        document.getElementById('m_route').textContent=d.pickup+" → "+d.dropoff;
        document.getElementById('m_pass').textContent=d.passengers+" người";
        document.getElementById('m_type').textContent=d.vehicle_type;
        document.getElementById('m_code').textContent=d.order_code;
        document.getElementById('detailModal').classList.add('active');
    });
}

function acceptTrip(){
    if(!confirm("Nhận đơn này ngay?"))return;
    const f=new FormData();f.append("pending_id",currentId);
    fetch("api/accept_trip.php",{method:"POST",body:f}).then(r=>r.json()).then(res=>{
        alert(res.message);
        if(res.success){closeModal();loadPending();loadAccepted();loadCharts();}
    });
}

async function loadPending(){
    const p=new URLSearchParams();
    const date=document.getElementById('date-filter').value;
    const pickup=document.getElementById('pickup-filter').value;
    const dropoff=document.getElementById('dropoff-filter').value;
    if(date)p.append('date',date);
    if(pickup)p.append('pickup',pickup);
    if(dropoff)p.append('dropoff',dropoff);

    const url = p.toString() ? `api/get_pending.php?${p}` : 'api/get_pending.php';
    const data = await (await fetch(url)).json();

    document.getElementById('pending-count').textContent = data.length;
    document.getElementById('pending-body').innerHTML = data.length ? data.map(t=>`
        <tr>
            <td data-label="Khách">${t.customer_name}</td>
            <td data-label="Đón">${t.pickup_location}</td>
            <td data-label="Trả">${t.dropoff_location}</td>
            <td data-label="Xe">${t.vehicle_type}</td>
            <td data-label="Số khách">${t.passengers}</td>
            <td data-label="Hành động">
                <a href="#" class="link" onclick="showDetail(${t.id});return false">Xem</a> | 
                <a href="#" class="link" onclick="currentId=${t.id};acceptTrip();return false">Nhận</a>
            </td>
            <td data-label="Trạng thái"><span style="color:#d32f2f">Chưa có tài xế</span></td>
        </tr>`).join('') : '<tr><td colspan="7" style="text-align:center;padding:80px;color:#999;font-size:18px">Không tìm thấy chuyến nào</td></tr>';
}

async function loadAccepted(){
    const data=await (await fetch('api/get_accepted.php')).json();
    document.getElementById('total-trips').textContent=data.length;
    const total=data.reduce((s,t)=>s+(parseFloat(t.revenue)||1500000),0);
    document.getElementById('total-revenue').textContent=total.toLocaleString('vi-VN')+' đ';
    document.getElementById('accepted-body').innerHTML=data.length?data.map(t=>`
        <tr>
            <td data-label="Mã">${t.order_code||'---'}</td>
            <td data-label="Đón">${t.pickup_location}</td>
            <td data-label="Trả">${t.dropoff_location}</td>
            <td data-label="Xe">${t.vehicle_type}</td>
            <td data-label="Khách">${t.passengers}</td>
            <td data-label="Ngày">${new Date(t.accept_date).toLocaleDateString('vi-VN')}</td>
            <td data-label="Thu">${(parseFloat(t.revenue)||1500000).toLocaleString('vi-VN')} đ</td>
        </tr>`).join(''):'<tr><td colspan="7" style="text-align:center;padding:80px;color:#999">Chưa nhận chuyến nào</td></tr>';
}

async function loadCharts(){
    const types=['month','quarter','year'];
    for(const type of types){
        const data=await (await fetch(`api/stats.php?type=${type}`)).json();
        const id=type==='year'?'chartYear':type==='quarter'?'chartQuarter':'chartMonth';
        if(charts[id])charts[id].destroy();
        charts[id]=new Chart(document.getElementById(id),{type:'bar',data:{labels:data.map(x=>x.label),datasets:[{label:'Số chuyến',data:data.map(x=>x.trips),backgroundColor:'#003087',borderRadius:8,barThickness:40}]},options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{display:false}},scales:{y:{beginAtZero:true,ticks:{stepSize:1}}}}});
    }
}

window.onload=()=>{
    document.getElementById('date-filter').valueAsDate=new Date();
    loadPending();loadAccepted();loadCharts();
    setInterval(()=>{loadPending();loadAccepted();},25000);
};
// Chuyển tab thống kê + làm đẹp nút active
function switchChart(type) {
    document.querySelectorAll('.chart-btn[data-type]').forEach(btn => {
        btn.classList.remove('active');
    });
    document.querySelector(`.chart-btn[data-type="${type}"]`).classList.add('active');
    loadCharts(); // Tự động cập nhật biểu đồ khi chuyển tab
}

// Khởi động: mặc định chọn "Tháng này"
document.addEventListener('DOMContentLoaded', () => {
    switchChart('month');
});
</script>
</body>
</html>