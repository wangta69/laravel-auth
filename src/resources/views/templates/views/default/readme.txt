# blade 설명
- cancel-account-success
> 계정삭제가 정상적으로 실행된 후 페이지

- cancel-account
> 계정 삭제 페이지

- edit-password
> 패스워드 변경 페이지

- edit
> 회원정보 변경페이지

- forgot-password
> 패스워드 찾기 페이지

- login
> 로그인페이지

- register-agreement
> 현재 default에서는 존재 하지 않는 것으로 회원가입전 개인보호정책등에 대한 동의를 받는페이지이다.
> 이 페이지가 존재하면 register를 클릭하더라도 register-agreement 페이지로 자동 이동한다.
> 예는 sample 폴더에 존재한다.

- profile
> 회원정보 페이지

- register-success
> 계정생성이 정상적으로 실행된 후 페이지

- register
> 계정생성 페이지

- reset-password
> 패스워드 변경페이지 
> 위의 edit-password은 로그인 상태에서 사용자가 패스워드를 변경할때 나오는 페이지인 반면 
> reset-password 는 forgot-password 를 통해 보내진 이메일에서 클릭시 나오는 페이지

- verify-email
> config / pondol-auth.activate 가 'email' 일 경우 회원가입후 이메일로 계정 활성페이지, 혹은 로그인 후 이메일로 계정 활성 페이지
