<?php if (!defined('THINK_PATH')) exit();?><!-- 销售漏斗 -->
<div class="span4 sort-item" rel="<?php echo ($id); ?>">
	<div class="dash-border">
		<div class="dash-title">
			<img src="__PUBLIC__/img/chart.png" style="width:17.5px;" />&nbsp;&nbsp;<?php echo ($title); ?>&nbsp;
			<small>
				<a rel="<?php echo ($id); ?>" class="update" href="javascript:void(0)" id="update_widget"><i class="icon-pencil"></i></a> &nbsp;
				<a rel="<?php echo ($id); ?>" class="delete_sales" style="cursor:pointer"><i class="icon-remove"></i></a> &nbsp; 
			</small>
			<a href="<?php echo U('business/analytics');?>" class="dash-swtich"> 切换到商机统计>></a>
		</div>
		<div class="cut-line"></div>
		<div class="content-chart" id="sales_funnel_<?php echo ($id); ?>">拼命加载中...</div>
	</div>
</div>
<script type="text/javascript" src="__PUBLIC__/js/chart/funnel.js"></script>
<script type="text/javascript">
	$('.delete_sales').click(function(){
		if(confirm('确定要删除吗？')){
			var id = $(this).attr('rel');
			window.location.href="index.php?m=index&a=widget_delete&id="+id;
		}else{
			return false;
		}
	});
	$(function () {
		var chart = new Highcharts.Chart({
			chart: {
				renderTo: 'sales_funnel_<?php echo ($id); ?>',
				type: 'funnel',
				marginRight: 100
			},
			title:false,
			plotOptions: {
				series: {
					dataLabels: {
						enabled: true,
						format: '<b>{point.name}</b> ({point.y:,.0f})',
						color: 'black',
						softConnector: true,
					},
					neckWidth: '30%',
					neckHeight: '25%'
				}
			},
			legend: {
				enabled: false
			},
			series: [{
				name: '数量',
				data: ''
			}],
			credits: {  
				enabled: false  
			} 
		});
		//使用JQuery从后台获取JSON格式的数据赋值到图标
		$.ajax({
			type: "get",
			url: "<?php echo U('business/getSalesFunnel');?>",    
			dataType: "json",
			success : function(result){
				chart.series[0].setData(result.data);
				chart.hideLoading();
				chart.redraw();
			}
		});
	});
</script>
<!-- 销售漏斗 END-->