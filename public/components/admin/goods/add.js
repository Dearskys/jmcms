Vue.component('Add', {
	template: `
		<div v-if="show">
			<el-form :size="size" ref="form" :model="form" :rules="rules" :label-width="'90px'">
			<el-tabs v-model="activeName">
				<el-tab-pane style="padding-top:10px"  label="基本信息" name="基本信息">
				<el-row >
					<el-col :span="24">
						<el-form-item label="商品名称" prop="goods_name">
							<el-input  v-model="form.goods_name" autoComplete="off" clearable  placeholder="请输入商品名称"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="所属分类" prop="class_id">
							<treeselect  v-if="show" :appendToBody="true" :default-expand-level="2" v-model="form.class_id" :options="class_ids" :normalizer="normalizer" :show-count="true" zIndex="999999" placeholder="请选择所属分类"/>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="封面图" prop="pic">
							<Upload v-if="show" size="small"      file_type="image"  :image.sync="form.pic"></Upload>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="销售价" prop="sale_price">
							<el-input  v-model="form.sale_price" autoComplete="off" clearable  placeholder="请输入销售价"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="图集" prop="images">
							<Upload v-if="show" size="small"    file_type="images" :images.sync="form.images"></Upload>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="内容详情" prop="detail" v-if="show">
							<tinymce  :content.sync="form.detail"></tinymce>
						</el-form-item>
					</el-col>
				</el-row>
				</el-tab-pane>
				<el-tab-pane style="padding-top:10px"  label="拓展信息" name="拓展信息">
				<el-row >
					<el-col :span="24">
						<el-form-item label="状态" prop="status">
							<el-switch :active-value="1" :inactive-value="0" v-model="form.status"></el-switch>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="产地" prop="cd">
							<el-input  v-model="form.cd" autoComplete="off" clearable  placeholder="请输入产地"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="库存" prop="store">
							<el-input  v-model="form.store" autoComplete="off" clearable  placeholder="请输入库存"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="排序" prop="sortid">
							<el-input-number controls-position="right" style="width:200px;" autoComplete="off" v-model="form.sortid" clearable :min="0" placeholder="请输入排序"/>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="发布时间" prop="create_time">
							<el-date-picker value-format="yyyy-MM-dd" type="date" v-model="form.create_time" clearable placeholder="请输入发布时间"></el-date-picker>
						</el-form-item>
					</el-col>
				</el-row>
				</el-tab-pane>
			</el-tabs>
				<el-form-item>
					<el-button :size="size" type="primary" @click="submit">保存设置</el-button>
					<el-button :size="size" icon="el-icon-back" @click="closeForm">返回</el-button>
				</el-form-item>
			</el-form>
		</div>
	`
	,
	components:{
		'treeselect':VueTreeselect.Treeselect,
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
	},
	data(){
		return {
			form: {
				goods_name:'',
				pic:'',
				sale_price:'',
				images:[],
				status:1,
				cd:'',
				store:'',
				create_time:'',
				detail:'',
			},
			class_ids:[],
			loading:false,
			activeName:'基本信息',
			rules: {
				goods_name:[
					{required: true, message: '商品名称不能为空', trigger: 'blur'},
				],
			}
		}
	},
	watch:{
		show(val){
			if(val){
				axios.post(base_url + '/Goods/getFieldList').then(res => {
					if(res.data.status == 200){
						this.class_ids = res.data.data.class_ids
					}
				})
			}
			if(val){
				this.open()
			}
		}
	},
	methods: {
		open(){
		},
		submit(){
			this.$refs['form'].validate(valid => {
				if(valid) {
					this.loading = true
					axios.post(base_url + '/Goods/add',this.form).then(res => {
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
		normalizer(node) {
			if (node.children && !node.children.length) {
				delete node.children
			}
			return {
				id: node.val,
				label: node.key,
				children: node.children
			}
		},
		closeForm(){
			this.$emit('update:show', false)
			this.loading = false
			if (this.$refs['form']!==undefined) {
				this.$refs['form'].resetFields()
			}
			this.$emit('changepage')
		},
	}
})
