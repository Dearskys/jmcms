Vue.component('Detail', {
	template: `
		<div v-if="show">
			<table cellpadding="0" cellspacing="0" class="table table-bordered" align="center" width="100%" style="word-break:break-all; margin-bottom:30px;  font-size:13px;">
				<tbody>
					<tr>
						<td class="title" width="100">商品名称：</td>
						<td>
							{{form.goods_name}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">分类名称：</td>
						<td>
							<span v-if="form.class_name">{{form.class_name}}</span>
						</td>
					</tr>
					<tr>
						<td class="title" width="100">封面图：</td>
						<td>
							<el-image v-if="form.pic" class="table_list_pic" :src="form.pic"  :preview-src-list="[form.pic]"></el-image>
						</td>
					</tr>
					<tr>
						<td class="title" width="100">销售价：</td>
						<td>
							{{form.sale_price}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">状态：</td>
						<td>
							<span v-if="form.status == '1'">开启</span>
							<span v-if="form.status == '0'">关闭</span>
						</td>
					</tr>
					<tr>
						<td class="title" width="100">排序：</td>
						<td>
							{{form.sortid}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">发布时间：</td>
						<td>
							{{parseTime(form.create_time,'{y}-{m}-{d}')}}
						</td>
					</tr>
				</tbody>
			</table>
			<el-button :size="size" icon="el-icon-back" @click="closeForm">返回</el-button>
		</div>
	`
	,
	props: {
		show: {
			type: Boolean,
			default: true
		},
		size: {
			type: String,
			default: 'mini'
		},
		info: {
			type: Object,
		},
	},
	data() {
		return {
			form:{
			},
		}
	},
	watch:{
		show(val){
			if(val){
				this.open()
			}
		}
	},
	methods: {
		open(){
			axios.post(base_url+'/Goods/detail',this.info).then(res => {
				this.form = res.data.data
			})
		},
		closeForm(){
			this.$emit('update:show', false)
			this.$emit('changepage')
		}
	}
})
