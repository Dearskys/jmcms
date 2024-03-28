Vue.component('Update', {
	template: `
		<el-dialog title="修改" width="600px" class="icon-dialog" :visible.sync="show" @open="open" :before-close="closeForm" append-to-body>
			<el-form :size="size" ref="form" :model="form" :rules="rules" :label-width=" ismobile()?'90px':'16%'">
				<el-row >
					<el-col :span="24">
						<el-form-item label="标题" prop="title">
							<el-input  v-model="form.title" autoComplete="off" clearable  placeholder="请输入标题"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="22">
						<el-form-item label="百度地图" class="map" prop="bddt">
							<el-input type="textarea"  v-model="form.bddt" placeholder="请选择坐标位置" @click.native="bddtDialogStatus = true" readonly clearable></el-input>
						</el-form-item>
					</el-col>
					<el-col :span="1">
						<div class="el-input-group__append" @click="bddtDialogStatus = true" style="height:50px;background-color:#fff;padding:0 13px;cursor:pointer">
							<i style="font-size:20px" class="el-icon-location"></i>
						</div>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="22">
						<el-form-item label="高德地图" class="map" prop="gddt">
							<el-input type="textarea"  v-model="form.gddt" placeholder="请选择坐标位置" @click.native="gddtDialogStatus = true" readonly clearable></el-input>
						</el-form-item>
					</el-col>
					<el-col :span="1">
						<div class="el-input-group__append" @click="gddtDialogStatus = true" style="height:50px;background-color:#fff;padding:0 13px;cursor:pointer">
							<i style="font-size:20px" class="el-icon-location"></i>
						</div>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="22">
						<el-form-item label="腾讯地图" class="map" prop="txdt">
							<el-input type="textarea"  v-model="form.txdt" placeholder="请选择坐标位置" @click.native="txdtDialogStatus = true" readonly clearable></el-input>
						</el-form-item>
					</el-col>
					<el-col :span="1">
						<div class="el-input-group__append" @click="txdtDialogStatus = true" style="height:50px;background-color:#fff;padding:0 13px;cursor:pointer">
							<i style="font-size:20px" class="el-icon-location"></i>
						</div>
					</el-col>
				</el-row>
			</el-form>
			<div slot="footer" class="dialog-footer">
				<el-button :size="size" :loading="loading" type="primary" @click="submit" >
					<span v-if="!loading">确 定</span>
					<span v-else>提 交 中...</span>
				</el-button>
				<el-button :size="size" @click="closeForm">取 消</el-button>
			</div>
			<baidu-map v-if="bddtDialogStatus" :show.sync="bddtDialogStatus" :address_detail.sync="form.bddt"></baidu-map>
			<gaode-map v-if="gddtDialogStatus" :show.sync="gddtDialogStatus" :address_detail.sync="form.gddt"></gaode-map>
			<tx-map v-if="txdtDialogStatus" :show.sync="txdtDialogStatus" :address_detail.sync="form.txdt"></tx-map>
		</el-dialog>
	`
	,
	components:{
	},
	props: {
		show: {
			type: Boolean,
			default: false
		},
		size: {
			type: String,
			default: 'small'
		},
		info: {
			type: Object,
		},
	},
	data(){
		return {
			form: {
				title:'',
				bddt:'',
				gddt:'',
				txdt:'',
			},
			bddtDialogStatus:false,
			gddtDialogStatus:false,
			txdtDialogStatus:false,
			loading:false,
			rules: {
			}
		}
	},
	methods: {
		open(){
			this.form = this.info
		},
		submit(){
			this.$refs['form'].validate(valid => {
				if(valid) {
					this.loading = true
					axios.post(base_url + '/Map/update',this.form).then(res => {
						if(res.data.status == 200){
							this.$message({message: res.data.msg, type: 'success'})
							this.$emit('refesh_list')
							this.closeForm()
						}else{
							this.loading = false
							this.$message.error(res.data.msg)
						}
					}).catch(()=>{
						this.loading = false
					})
				}
			})
		},
		closeForm(){
			this.$emit('update:show', false)
			this.loading = false
			if (this.$refs['form']!==undefined) {
				this.$refs['form'].resetFields()
			}
		},
	}
})
