<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Auth;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use App\Models\User;


class Company extends Component
{
    use WithFileUploads;

    public $currentStep = 1;
    public $company;
    public $username,$email,$password,$password_confirmation;
    public $name, $price, $detail, $status = 1;
    public  $logo,$license,$info,$phone,$address;
    public $successMsg = '';
    public $message="";

    public $ceo_name='';
    public $incharge_name,$incharge_position,$incharge_phone;
    public $service_label_one,
            $service_label_two,
            $service_label_three;

    public $service_desc_one,
            $service_desc_two,
            $service_desc_three;

    public function __construct(){
        if(Auth::check()){
            $this->currentStep++;
        }
    }
    

    public function render()
    {
        
        return view('livewire.company');
    }
    /**
     * Write code on Method
     */
    public function firstStepSubmit()
    {
        $validatedData = $this->validate([
             'username' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // dd('you made it');
        $user = User::create([
            'name' => $this->username,
            'email' => $this->email,
            'password' => Hash::make($this->password),
        ]);

        $user->assignRole('company');

        event(new Registered($user));

        Auth::login($user);

        $this->currentStep = 2;
    }
  
    /**
     * Write code on Method
     */
    public function secondStepSubmit()
    {
        // dd('helo');

        if(Auth::user()->hasRole('company')){
            if(empty(Auth::user()->company)){
                $validatedData = $this->validate([
                    'name' => 'required|unique:companies',
                    'logo' => 'required|image|max:1024',
                    'license' => 'required|image|max:1024',
                    'info' => 'required',
                    'phone' => 'required',
                    'address' => 'required',
                ]);
                $filename=time();

                $path = $this->logo->storeAs('logo',$filename,'public');
                 $license = $this->logo->storeAs('license','l-'.$filename,'public');

                
                // if(Auth::check() && Auth::user()->hasRole()){

                // }
                 
                 $this->company=\App\Models\Company::create([
                    'name'=>$this->name,
                    'logo'=>$path,
                    'photo'=>$license,
                    'info'=>$this->info,
                    'phone'=>$this->phone,
                    'addresss'=>$this->address,
                    'status'=>1,
                    'user_id'=>Auth::user()->id
                 ]);
                  // $this->successMsg = 'Company already exists!';
                 $this->currentStep=3;
                
            }else{
                
                $this->currentStep=1;
                $this->successMsg = 'Company already exists!';
                
                
            }
        }

         $this->currentStep=3;
          $this->company=Auth::user()->company; 
           // dd($this->currentStep);
        // dd('helo');
        
        
    }
  
    /**
     * Write code on Method
     */
    public function submitForm()
    {

        // dd($this->company->name);
        // dd($this->ceo_name);
        dd($this->message);
        
       // dd($this->message);
        $dom=new \DomDocument();

        dd($this->service_desc_one);
        
        $this->company->ceo_name=$this->ceo_name;
        $this->company->incharge_name=$this->incharge_name;
        $this->company->incharge_position=$this->incharge_position;
        $this->company->incharge_phone=$this->incharge_phone;
        $this->company->service_label_one=$this->service_label_one;
        $this->company->service_label_two=$this->service_label_two;
        $this->company->service_label_three=$this->service_label_three;

        $dom->loadHTML($this->service_desc_one,LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

         $this->company->service_desc_one=$dom->saveHTML();

         $dom->loadHTML($this->service_desc_two,LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

         $this->company->service_desc_two=$dom->saveHTML();
         
      
        $dom->loadHTML($this->service_desc_three,LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

        $this->company->service_desc_three=$dom->saveHTML();
        
        $this->save();

        $this->successMsg = 'Team successfully created.';
  
        $this->clearForm();
  
        dd('yes you make it!');
        $this->emit('reinit');
    }
  
    /**
     * Write code on Method
     */
    public function back($s)
    {
        $this->currentStep=$s;   
    }

    public function skip(){
        $this->currentStep = 1;
        $this->clearForm();
        return redirect()->route('car.index');
    }
  
    /**
     * Write code on Method
     */
    public function clearForm()
    {
        $this->name = '';
        $this->price = '';
        $this->detail = '';
        $this->status = 1;
    }
}
